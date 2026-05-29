<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Report;
use App\Models\SocialGroup;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Thống kê người dùng
        $totalUsers = User::count();
        $studentCount = User::where('user_type', 'student')->count();
        $teacherCount = User::where('user_type', 'teacher')->count();
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
                                 ->whereYear('created_at', Carbon::now()->year)
                                 ->count();

        // 2. Thống kê bài viết
        $activePosts = Post::count();
        $deletedPosts = Post::onlyTrashed()->count();

        // 3. Thống kê báo cáo
        $pendingReports = Report::where('status', 'pending')->count();

        // 4. Tài khoản bị đánh dấu
        $lockedAccounts = User::where('status', 'flagged')->count();

        // 5. Cộng đồng
        $totalGroups = SocialGroup::count();

        // Dữ liệu cho biểu đồ Người dùng mới (7 ngày gần nhất)
        $userChartData = [];
        $postChartData = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            
            $userChartData[] = User::whereDate('created_at', $date)->count();
            $postChartData[] = Post::whereDate('created_at', $date)->count();
        }

        // Top người đăng bài nhiều nhất
        $topPosters = User::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(5)
            ->get();

        // Báo cáo mới nhất
        $latestReports = Report::with(['reporter'])->where('status', 'pending')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'studentCount', 'teacherCount', 'newUsersThisMonth',
            'activePosts', 'deletedPosts', 'pendingReports', 'lockedAccounts',
            'totalGroups', 'labels', 'userChartData', 'postChartData',
            'topPosters', 'latestReports'
        ));
    }
}
