<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Like;
use App\Models\Repost;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Participant;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    /**
     * Danh sách tất cả tài khoản
     */
    public function index(Request $request)
    {
        $query = User::with(['student', 'teacher.faculty']);

        // Tìm kiếm theo username, email, hoặc tên đầy đủ
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('full_name', 'like', "%$search%");
                  })
                  ->orWhereHas('teacher', function($tq) use ($search) {
                      $tq->where('full_name', 'like', "%$search%");
                  });
            });
        }

        // Lọc theo ngày tháng năm sinh (chỉ dành cho sinh viên)
        if ($request->filled('dob')) {
            $query->whereHas('student', function($sq) use ($request) {
                $sq->whereDate('dob', $request->dob);
            });
        }

        // Thống kê
        $stats = [
            'total' => User::count(),
            'students' => User::where('user_type', 'student')->count(),
            'teachers' => User::where('user_type', 'teacher')->count(),
            'flagged' => User::where('status', 'flagged')->count(),
        ];

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Danh sách tài khoản bị đánh dấu
     */
    public function flagged(Request $request)
    {
        $query = User::where('status', 'flagged')->with(['student', 'teacher']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.flagged', compact('users'));
    }

    /**
     * Đánh dấu hoặc bỏ đánh dấu tài khoản
     */
    public function toggleFlag($id)
    {
        $user = User::findOrFail($id);
        
        // Không cho phép đánh dấu chính mình (admin hiện tại)
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể tự đánh dấu chính mình.');
        }

        $newStatus = ($user->status === 'flagged') ? 'active' : 'flagged';
        $user->update(['status' => $newStatus]);

        $msg = $newStatus === 'flagged' ? 'Đã đánh dấu tài khoản.' : 'Đã bỏ đánh dấu tài khoản.';
        return back()->with('success', $msg);
    }

    /**
     * Giao diện Import tài khoản
     */
    public function importIndex()
    {
        return view('admin.users.import');
    }

    /**
     * Import đơn lẻ một tài khoản
     */
    public function importSingle(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'full_name' => 'required|string|max:100',
            'user_type' => 'required|in:student,teacher',
            'faculty_id' => 'required|exists:faculties,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password_hash' => \Illuminate\Support\Facades\Hash::make('123456@'),
                    'user_type' => $request->user_type,
                    'status' => 'active',
                ]);

                if ($request->user_type === 'student') {
                    \App\Models\StudentDetail::create([
                        'user_id' => $user->id,
                        'student_id' => $request->student_id,
                        'full_name' => $request->full_name,
                        'dob' => $request->dob,
                        'class' => $request->class,
                        'faculty_id' => $request->faculty_id,
                    ]);
                } else {
                    \App\Models\TeacherDetail::create([
                        'user_id' => $user->id,
                        'full_name' => $request->full_name,
                        'faculty_id' => $request->faculty_id,
                    ]);
                }
            });

            return back()->with('success', 'Đã tạo tài khoản thành công với mật khẩu mặc định 123456@');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Import hàng loạt từ file CSV
     */
    public function importBulk(Request $request)
    {
        set_time_limit(600); // Tăng thời gian thực thi lên 10 phút

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'user_type' => 'required|in:student,teacher',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle); // Đọc dòng đầu (header)

        $count = 0;
        $line = 1; // Dòng 1 là header
        $errors = [];
        // Pre-hash password để tăng tốc độ xử lý
        $defaultPassword = \Illuminate\Support\Facades\Hash::make('123456@');

        try {
            DB::transaction(function () use ($handle, $request, &$count, &$line, &$errors, $defaultPassword) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    $line++;
                    if (empty($data[0]) && count($data) <= 1) continue; 

                    // Trim dữ liệu để tránh lỗi khoảng trắng thừa
                    $data = array_map('trim', $data);

                    try {
                        // Kiểm tra trùng lặp trước khi insert để tránh lỗi SQL Integrity Constraint
                        $existingUser = User::where('username', $data[0])
                            ->orWhere('email', $data[1])
                            ->first();

                        if ($existingUser) {
                            $errors[] = "Dòng $line: Tài khoản '{$data[0]}' hoặc email '{$data[1]}' đã tồn tại.";
                            continue;
                        }

                        if ($request->user_type === 'student') {
                            if (count($data) < 7) {
                                throw new \Exception("Dữ liệu không đủ cột (yêu cầu 7 cột).");
                            }
                            
                            $user = User::create([
                                'username' => $data[0],
                                'email' => $data[1],
                                'password_hash' => $defaultPassword,
                                'user_type' => 'student',
                                'status' => 'active',
                            ]);
                            \App\Models\StudentDetail::create([
                                'user_id' => $user->id,
                                'full_name' => $data[2],
                                'student_id' => $data[3],
                                'dob' => $data[4],
                                'class' => $data[5],
                                'faculty_id' => $data[6],
                            ]);
                        } else {
                            if (count($data) < 4) {
                                throw new \Exception("Dữ liệu không đủ cột (yêu cầu 4 cột).");
                            }
                            
                            $user = User::create([
                                'username' => $data[0],
                                'email' => $data[1],
                                'password_hash' => $defaultPassword,
                                'user_type' => 'teacher',
                                'status' => 'active',
                            ]);
                            \App\Models\TeacherDetail::create([
                                'user_id' => $user->id,
                                'full_name' => $data[2],
                                'faculty_id' => $data[3],
                            ]);
                        }
                        $count++;
                    } catch (\Exception $e) {
                        $errors[] = "Lỗi dòng $line: " . $e->getMessage();
                    }
                }
            });
            fclose($handle);

            if (count($errors) > 0) {
                $errorMsg = count($errors) > 10 
                    ? implode('<br>', array_slice($errors, 0, 10)) . '<br>... và ' . (count($errors) - 10) . ' lỗi khác.'
                    : implode('<br>', $errors);
                
                return back()->with('success', "Đã import thành công $count tài khoản.")
                             ->with('error', "Phát hiện " . count($errors) . " lỗi:<br>" . $errorMsg);
            }
            return back()->with('success', "Đã import thành công $count tài khoản.");
        } catch (\Exception $e) {
            if (isset($handle) && is_resource($handle)) fclose($handle);
            return back()->with('error', 'Lỗi nghiêm trọng: ' . $e->getMessage());
        }
    }

    /**
     * Tải file mẫu CSV cho Sinh viên
     */
    public function downloadStudentTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mau_import_sinh_vien.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Header: username,email,full_name,student_id,dob,class,faculty_id
            fputcsv($file, ['username', 'email', 'full_name', 'student_id', 'dob(yyyy-mm-dd)', 'class', 'faculty_id']);
            // Sample row
            fputcsv($file, ['sv_nguyena', 'nguyenvana@eaut.edu.vn', 'Nguyễn Văn A', '22100001', '2004-01-01', 'CNTT14-01', '1']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tải file mẫu CSV cho Giảng viên
     */
    public function downloadTeacherTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mau_import_giang_vien.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Header: username,email,full_name,faculty_id
            fputcsv($file, ['username', 'email', 'full_name', 'faculty_id']);
            // Sample row
            fputcsv($file, ['gv_tranb', 'tranb@gmail.com', 'Trần Văn B', '1']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xóa tài khoản hoàn toàn và tất cả dữ liệu liên quan
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể xóa chính mình.');
        }

        DB::transaction(function () use ($user) {
            // 1. Xóa các tương tác
            Like::where('user_id', $user->id)->delete();
            Repost::where('user_id', $user->id)->delete();
            Follow::where('follower_id', $user->id)->orWhere('following_id', $user->id)->delete();
            
            // 2. Xóa thông báo
            Notification::where('user_id', $user->id)->delete();

            // 3. Xóa bình luận
            Comment::where('user_id', $user->id)->delete();

            // 4. Xóa bài viết (bao gồm cả media qua observer hoặc thủ công nếu cần)
            // Ở đây ta xóa cứng bài viết
            Post::where('user_id', $user->id)->forceDelete();

            // 5. Xóa tin nhắn và tham gia hội thoại
            Message::where('user_id', $user->id)->delete();
            Participant::where('user_id', $user->id)->delete();
            
            // 6. Xóa tham gia nhóm
            GroupMember::where('user_id', $user->id)->delete();

            // 7. Xóa User (Laravel sẽ tự cascade student_details/teacher_details do DB constraint)
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', 'Đã xóa hoàn toàn tài khoản và tất cả dữ liệu liên quan.');
    }
}
