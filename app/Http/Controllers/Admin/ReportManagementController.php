<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Message;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with(['reporter']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by month
        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereYear('created_at', $date->year)
                  ->whereMonth('created_at', $date->month);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'post_reports' => Report::where('type', 'post')->count(),
            'comment_reports' => Report::where('type', 'comment')->count(),
            'message_reports' => Report::where('type', 'message')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }

    public function destroyContent($id)
    {
        $report = Report::findOrFail($id);
        $type = $report->type;
        $contentId = $report->reported_id;

        try {
            if ($type === 'post') {
                $content = Post::find($contentId);
            } elseif ($type === 'comment') {
                $content = Comment::find($contentId);
            } elseif ($type === 'message') {
                $content = Message::find($contentId);
            }

            if ($content) {
                $content->delete();
                $report->update(['status' => 'resolved']);
                return back()->with('success', 'Nội dung đã được xóa thành công.');
            }

            return back()->with('error', 'Không tìm thấy nội dung để xóa hoặc đã bị xóa trước đó.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function ignoreReport($id)
    {
        $report = Report::findOrFail($id);
        $report->update(['status' => 'ignored']);
        return back()->with('success', 'Đã bỏ qua báo cáo này.');
    }
}
