<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportManagementController extends Controller
{
    public function index(Request $request)
    {
        // Bộ lọc chỉ lấy báo cáo của những nội dung tồn tại (chưa bị xóa)
        $applyExistsFilter = function($query) {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('reports.type', 'post')
                        ->whereExists(function ($ex) {
                            $ex->select(DB::raw(1))
                               ->from('posts')
                               ->whereColumn('posts.id', 'reports.reported_id')
                               ->whereNull('posts.deleted_at');
                        });
                })
                ->orWhere(function ($sub) {
                    $sub->where('reports.type', 'comment')
                        ->whereExists(function ($ex) {
                            $ex->select(DB::raw(1))
                               ->from('comments')
                               ->whereColumn('comments.id', 'reports.reported_id');
                        });
                })
                ->orWhere(function ($sub) {
                    $sub->where('reports.type', 'message')
                        ->whereExists(function ($ex) {
                            $ex->select(DB::raw(1))
                               ->from('messages')
                               ->whereColumn('messages.id', 'reports.reported_id')
                               ->whereNull('messages.deleted_at');
                        });
                });
            });
        };

        // ── Lấy danh sách unique (type, reported_id) đã bị báo cáo ──
        // Mỗi nội dung chỉ xuất hiện 1 lần, kèm thống kê tổng hợp (chỉ lấy các báo cáo có trạng thái pending)
        $query = DB::table('reports')
            ->tap($applyExistsFilter)
            ->where('status', 'pending')
            ->select(
                'type',
                'reported_id',
                DB::raw('COUNT(*) as report_count'),
                DB::raw('GROUP_CONCAT(DISTINCT reason ORDER BY reason SEPARATOR ",") as reasons_list'),
                DB::raw('MAX(created_at) as latest_report_at'),
                DB::raw('"pending" as status')
            )
            ->groupBy('type', 'reported_id');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by reason (nếu bất kỳ report nào trong group có lý do đó)
        if ($request->filled('reason')) {
            $query->whereExists(function ($sub) use ($request) {
                $sub->select(DB::raw(1))
                    ->from('reports as r2')
                    ->whereColumn('r2.reported_id', 'reports.reported_id')
                    ->whereColumn('r2.type', 'reports.type')
                    ->where('r2.reason', $request->reason);
            });
        }

        // Đã lược bỏ bộ lọc status do chỉ hiển thị các báo cáo pending

        // Filter by date / month (theo lần báo cáo mới nhất)
        if ($request->filled('date')) {
            $query->havingRaw('DATE(MAX(created_at)) = ?', [$request->date]);
        }
        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->havingRaw('YEAR(MAX(created_at)) = ? AND MONTH(MAX(created_at)) = ?', [$date->year, $date->month]);
        }

        $groupedReports = $query->orderByDesc('latest_report_at')->paginate(15)->withQueryString();

        // Load nội dung thực tế cho mỗi group
        foreach ($groupedReports as $row) {
            if ($row->type === 'post') {
                $row->content = Post::with('media')->find($row->reported_id);
            } elseif ($row->type === 'comment') {
                $row->content = Comment::find($row->reported_id);
            } elseif ($row->type === 'message') {
                $row->content = Message::find($row->reported_id);
            } else {
                $row->content = null;
            }
            // Parse reasons_list thành array
            $row->reasons_array = $row->reasons_list ? explode(',', $row->reasons_list) : [];
        }

        // Stats tổng quan (chỉ đếm các báo cáo chưa xử lý - pending)
        $stats = [
            'total'           => DB::table('reports')->tap($applyExistsFilter)->where('status', 'pending')->select('type', 'reported_id')->distinct()->count(),
            'pending'         => DB::table('reports')->tap($applyExistsFilter)->where('status', 'pending')->select('type', 'reported_id')->distinct()->count(),
            'post_reports'    => DB::table('reports')->tap($applyExistsFilter)->where('status', 'pending')->where('type', 'post')->select('reported_id')->distinct()->count(),
            'comment_reports' => DB::table('reports')->tap($applyExistsFilter)->where('status', 'pending')->where('type', 'comment')->select('reported_id')->distinct()->count(),
            'message_reports' => DB::table('reports')->tap($applyExistsFilter)->where('status', 'pending')->where('type', 'message')->select('reported_id')->distinct()->count(),
        ];

        // Thống kê theo lý do (chỉ tính báo cáo pending)
        $reasonStats = [];
        foreach (array_keys(Report::$REASONS) as $reasonKey) {
            // Đếm số nội dung độc lập bị báo cáo với lý do đó
            $reasonStats[$reasonKey] = DB::table('reports')
                ->tap($applyExistsFilter)
                ->where('status', 'pending')
                ->where('reason', $reasonKey)
                ->select('type', 'reported_id')
                ->distinct()
                ->count();
        }

        $reasons = Report::$REASONS;

        return view('admin.reports.index', compact('groupedReports', 'stats', 'reasonStats', 'reasons'));
    }

    public function destroyContent(Request $request)
    {
        $type      = $request->input('type');
        $contentId = $request->input('reported_id');

        try {
            if ($type === 'post') {
                $content = Post::find($contentId);
            } elseif ($type === 'comment') {
                $content = Comment::find($contentId);
            } elseif ($type === 'message') {
                $content = Message::find($contentId);
            } else {
                return back()->with('error', 'Loại nội dung không hợp lệ.');
            }

            if ($content) {
                $content->delete();
            }

            // Đánh dấu toàn bộ báo cáo của nội dung này là resolved
            Report::where('reported_id', $contentId)
                ->where('type', $type)
                ->update(['status' => 'resolved']);

            return back()->with('success', 'Nội dung đã được xóa và tất cả báo cáo liên quan đã được đánh dấu xử lý.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function ignoreReport(Request $request)
    {
        $type      = $request->input('type');
        $contentId = $request->input('reported_id');

        // Bỏ qua toàn bộ báo cáo của nội dung này
        Report::where('reported_id', $contentId)
            ->where('type', $type)
            ->update(['status' => 'ignored']);

        return back()->with('success', 'Đã bỏ qua tất cả báo cáo cho nội dung này.');
    }
}
