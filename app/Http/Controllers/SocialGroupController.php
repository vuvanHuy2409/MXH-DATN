<?php

namespace App\Http\Controllers;

use App\Models\SocialGroup;
use App\Models\Post;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SocialGroupController extends Controller
{
    public function index(Request $request, $slug = null)
    {
        $myGroups = Auth::user()->socialGroups()->withCount('members')->get();
        $faculties = \App\Models\Faculty::all();
        $studentDetails = StudentDetail::select('faculty_id', 'class')->distinct()->get();
        
        // Nếu không có slug, tự động chọn nhóm đầu tiên (nếu có)
        if (!$slug && $myGroups->isNotEmpty()) {
            return redirect()->route('groups.index', $myGroups->first()->slug);
        }

        $activeGroup = null;
        $posts = collect();
        $isMember = false;

        if ($slug) {
            $activeGroup = SocialGroup::where('slug', $slug)->withCount('members')->firstOrFail();
            $isMember = $activeGroup->members()->where('user_id', Auth::id())->exists();
            $posts = $activeGroup->posts()
                ->with(['user', 'media', 'likes'])
                ->withCount('comments')
                ->orderByRaw('user_id = ? DESC', [Auth::id()]) // Ưu tiên bài viết của mình lên đầu
                ->latest()
                ->get();
        }
        
        return view('groups.index', compact('myGroups', 'faculties', 'studentDetails', 'activeGroup', 'posts', 'isMember'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:community,class',
            'name' => 'required|string|max:255|unique:social_groups,name',
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required_if:type,community|in:public,private',
            'class_name' => 'required_if:type,class|string|max:50',
        ]);

        // Kiểm tra quyền: Chỉ giáo viên mới được tạo nhóm lớp
        if ($request->type === 'class' && Auth::user()->user_type !== 'teacher') {
            return back()->withErrors(['type' => 'Chỉ giáo viên mới có quyền tạo cộng đồng theo lớp học.'])->withInput();
        }

        $group = DB::transaction(function () use ($request) {
            $privacy = $request->type === 'class' ? 'private' : $request->privacy;
            
            $group = SocialGroup::create([
                'type' => $request->type,
                'name' => $request->name,
                'class_name' => $request->type === 'class' ? $request->class_name : null,
                'description' => $request->description,
                'privacy' => $privacy,
                'creator_id' => Auth::id(),
                'avatar_url' => $request->type === 'class' ? '/uploads/groups/class-default.png' : '/uploads/groups/community-default.png',
            ]);

            $group->members()->attach(Auth::id(), ['role' => 'admin']);

            if ($request->type === 'class') {
                $studentUserIds = StudentDetail::where('class', $request->class_name)
                    ->where('user_id', '!=', Auth::id())
                    ->pluck('user_id')
                    ->toArray();
                
                if (!empty($studentUserIds)) {
                    $group->members()->attach($studentUserIds, ['role' => 'member']);
                }
            }
            return $group;
        });

        return redirect()->route('groups.show', $group->slug)->with('status', 'Nhóm đã được tạo thành công!');
    }

    public function joinByCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:5']);
        $code = strtoupper($request->code);
        $group = SocialGroup::where('join_code', $code)->first();
        
        if (!$group) return back()->withErrors(['code' => 'Mã nhóm không chính xác.']);

        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            $group->members()->attach(Auth::id(), ['role' => 'member']);
        }

        return redirect()->route('groups.show', $group->slug)->with('status', 'Bạn đã tham gia nhóm thành công!');
    }

    public function join(SocialGroup $group)
    {
        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            $group->members()->attach(Auth::id(), ['role' => 'member']);
        }
        return back()->with('status', 'Bạn đã tham gia nhóm ' . $group->name);
    }

    public function leave(SocialGroup $group)
    {
        if ($group->creator_id === Auth::id()) return back()->withErrors(['group' => 'Trưởng nhóm không thể rời.']);
        $group->members()->detach(Auth::id());
        return redirect()->route('groups.index')->with('status', 'Bạn đã rời khỏi nhóm.');
    }

    public function getMembers(SocialGroup $group)
    {
        $members = $group->members()->select('users.id', 'username', 'avatar_url')->withPivot('role')->get();
        return response()->json($members);
    }

    public function searchUsers(Request $request, SocialGroup $group)
    {
        $query = $request->query('q');
        $existingMemberIds = $group->members()->pluck('users.id')->toArray();

        $users = \App\Models\User::where('username', 'LIKE', "%{$query}%")
            ->whereNotIn('id', $existingMemberIds)
            ->limit(10)
            ->get(['id', 'username', 'avatar_url']);

        return response()->json($users);
    }

    public function addMember(Request $request, SocialGroup $group)
    {
        if (!$group->members()->where('user_id', Auth::id())->where('role', 'admin')->exists()) {
            return response()->json(['message' => 'Bạn không có quyền thêm thành viên.'], 403);
        }

        $userId = $request->input('user_id');
        if (!$group->members()->where('user_id', $userId)->exists()) {
            $group->members()->attach($userId, ['role' => 'member']);
        }

        return response()->json(['message' => 'Đã thêm thành viên thành công.']);
    }

    public function updateAvatar(Request $request, SocialGroup $group)
    {
        if ($group->creator_id !== Auth::id()) abort(403);

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $group->id . '.' . $file->getClientOriginalExtension();
            
            // Ensure the directory exists
            if (!file_exists(public_path('uploads/groups/avatars'))) {
                mkdir(public_path('uploads/groups/avatars'), 0755, true);
            }
            
            $file->move(public_path('uploads/groups/avatars'), $filename);
            
            // Delete old avatar if it's not a default one
            if ($group->avatar_url && !str_contains($group->avatar_url, 'default.png')) {
                $oldPath = public_path($group->avatar_url);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $group->update([
                'avatar_url' => '/uploads/groups/avatars/' . $filename
            ]);
        }

        return back()->with('status', 'Ảnh nhóm đã được cập nhật thành công!');
    }

    public function update(Request $request, SocialGroup $group)
    {
        if ($group->creator_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255|unique:social_groups,name,' . $group->id,
            'description' => 'nullable|string|max:1000',
            'privacy' => 'required|in:public,private',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'privacy' => $request->privacy,
        ]);

        return back()->with('status', 'Thông tin nhóm đã được cập nhật.');
    }

    public function destroy(SocialGroup $group)
    {
        if ($group->creator_id !== Auth::id()) abort(403);

        $group->delete();

        return redirect()->route('groups.index')->with('status', 'Nhóm đã được xóa vĩnh viễn.');
    }
}
