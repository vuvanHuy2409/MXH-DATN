@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <!-- STAT CARDS - ROW 1 -->
    <div class="grid grid-cols-5 gap-4 mb-4">
        <!-- Total Users -->
        <div class="bg-white rounded-xl p-5 stat-card">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-[#3B5BDB]"></i>
                </div>
            </div>
            <h3 class="text-[28px] font-bold text-[#111827]">{{ number_format($totalUsers) }}</h3>
            <p class="text-[12px] text-[#9CA3AF] mt-1">Tổng người dùng</p>
            <p class="text-[11px] text-[#9CA3AF] mt-1">+{{ $newUsersThisMonth }} tháng này</p>
        </div>

        <!-- Students -->
        <div class="bg-white rounded-xl p-5 stat-card">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-[#7C3AED]"></i>
                </div>
            </div>
            <h3 class="text-[28px] font-bold text-[#111827]">{{ number_format($studentCount) }}</h3>
            <p class="text-[12px] text-[#9CA3AF] mt-1">Sinh viên</p>
            <p class="text-[11px] text-[#9CA3AF] mt-1">{{ $teacherCount }} giảng viên</p>
        </div>

        <!-- Active Posts -->
        <div class="bg-white rounded-xl p-5 stat-card">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5 text-[#2563EB]"></i>
                </div>
            </div>
            <h3 class="text-[28px] font-bold text-[#111827]">{{ number_format($activePosts) }}</h3>
            <p class="text-[12px] text-[#9CA3AF] mt-1">Bài viết đang hiện</p>
            <p class="text-[11px] text-[#9CA3AF] mt-1">{{ $deletedPosts }} đã xóa</p>
        </div>

        <!-- Reports -->
        <div class="bg-white rounded-xl p-5 stat-card">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-[#F59E0B]"></i>
                </div>
            </div>
            <h3 class="text-[28px] font-bold text-[#111827]">{{ $pendingReports }}</h3>
            <p class="text-[12px] text-[#9CA3AF] mt-1">Báo cáo chờ xử lý</p>
            <p class="text-[11px] text-[#9CA3AF] mt-1 text-xs">
                @if($pendingReports > 0)
                    Có báo cáo mới cần xử lý
                @else
                    Không có báo cáo mới
                @endif
            </p>
        </div>

        <!-- Locked Accounts -->
        <div class="bg-white rounded-xl p-5 stat-card">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                    <i data-lucide="lock" class="w-5 h-5 text-[#EF4444]"></i>
                </div>
            </div>
            <h3 class="text-[28px] font-bold text-[#111827]">{{ $lockedAccounts }}</h3>
            <p class="text-[12px] text-[#9CA3AF] mt-1">Tài khoản bị đánh dấu</p>
            <p class="text-[11px] text-[#9CA3AF] mt-1">trên tổng {{ $totalUsers }}</p>
        </div>
    </div>

    <!-- STAT CARDS - ROW 2 -->
    <div class="grid grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 stat-card">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-[#3B5BDB]"></i>
                </div>
            </div>
            <h3 class="text-[28px] font-bold text-[#111827]">{{ $totalGroups }}</h3>
            <p class="text-[12px] text-[#9CA3AF] mt-1">Cộng đồng nhóm lớp + cộng đồng</p>
        </div>
    </div>

    <!-- CHARTS - ROW 3 -->
    <div class="grid grid-cols-2 gap-6 mb-6">
        <!-- User Growth Chart -->
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-[14px] font-semibold text-[#111827]">Người dùng mới – 7 ngày</h4>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#3B5BDB]"></span>
                    <span class="text-[12px] text-[#6B7280]">Người dùng</span>
                </div>
            </div>
            <div class="h-[200px]">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Post Growth Chart -->
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-[14px] font-semibold text-[#111827]">Bài viết mới – 7 ngày</h4>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#7C3AED]"></span>
                    <span class="text-[12px] text-[#6B7280]">Bài viết</span>
                </div>
            </div>
            <div class="h-[200px]">
                <canvas id="postGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- TABLES - ROW 4 -->
    <div class="grid grid-cols-2 gap-6">
        <!-- Top Posters -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 px-6 flex items-center justify-between border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <i data-lucide="trophy" class="w-4 h-4 text-[#F59E0B]"></i>
                    <h4 class="text-[14px] font-bold text-[#111827]">Top người đăng bài nhiều nhất</h4>
                </div>
            </div>
            <table class="w-full text-left">
                <thead class="bg-[#F9FAFB] text-[11px] uppercase text-[#6B7280] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">NGƯỜI DÙNG</th>
                        <th class="px-6 py-3">LOẠI</th>
                        <th class="px-6 py-3">BÀI VIẾT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-[13px]">
                    @foreach($topPosters as $index => $poster)
                    <tr>
                        <td class="px-6 py-4 text-[#6B7280]">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-[#111827]">{{ $poster->username }}</td>
                        <td class="px-6 py-4">
                            <span class="{{ $poster->user_type === 'teacher' ? 'bg-purple-50 text-[#7C3AED]' : 'bg-blue-50 text-[#3B5BDB]' }} px-2 py-0.5 rounded-full text-[11px]">
                                {{ $poster->user_type === 'teacher' ? 'Giảng viên' : 'Sinh viên' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-[#111827]">{{ $poster->posts_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Latest Reports -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 px-6 flex items-center justify-between border-b border-gray-50">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <h4 class="text-[14px] font-bold text-[#111827]">Báo cáo mới nhất</h4>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="text-[12px] font-medium text-[#3B5BDB] border border-[#3B5BDB]/20 px-3 py-1 rounded-md hover:bg-[#EEF2FF] transition-colors">Xem tất cả</a>
            </div>
            @if($latestReports->count() > 0)
                <table class="w-full text-left">
                    <thead class="bg-[#F9FAFB] text-[11px] uppercase text-[#6B7280] font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3">NGƯỜI BÁO CÁO</th>
                            <th class="px-6 py-3">LÝ DO</th>
                            <th class="px-6 py-3">LOẠI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-[13px]">
                        @foreach($latestReports as $report)
                        <tr>
                            <td class="px-6 py-4 font-medium text-[#111827]">{{ $report->reporter->username ?? 'Ẩn danh' }}</td>
                            <td class="px-6 py-4 text-[#6B7280]">{{ Str::limit($report->reason, 30) }}</td>
                            <td class="px-6 py-4">
                                <span class="text-[11px] font-semibold uppercase">
                                    @if($report->type === 'post') Bài viết
                                    @elseif($report->type === 'comment') Bình luận
                                    @else Tin nhắn
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-12 text-center text-[#9CA3AF]">
                    <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                    <p class="text-[13px]">Không có báo cáo nào cần xử lý</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Dữ liệu từ PHP
        const chartLabels = {!! json_encode($labels) !!};
        const userData = {!! json_encode($userChartData) !!};
        const postData = {!! json_encode($postChartData) !!};

        // Biểu đồ Người dùng mới
        const ctxUser = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(ctxUser, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Người dùng',
                    data: userData,
                    borderColor: '#3B5BDB',
                    borderWidth: 2,
                    pointBackgroundColor: '#3B5BDB',
                    pointRadius: 3,
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { display: false }, ticks: { font: { size: 10 }, color: '#9CA3AF', stepSize: 1 } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9CA3AF' } }
                }
            }
        });

        // Biểu đồ Bài viết mới
        const ctxPost = document.getElementById('postGrowthChart').getContext('2d');
        new Chart(ctxPost, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Bài viết',
                    data: postData,
                    borderColor: '#7C3AED',
                    borderWidth: 2,
                    pointBackgroundColor: '#7C3AED',
                    pointRadius: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: (context) => {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return null;
                        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(124, 58, 237, 0.15)');
                        gradient.addColorStop(1, 'rgba(124, 58, 237, 0)');
                        return gradient;
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F3F4F6' }, ticks: { font: { size: 10 }, color: '#9CA3AF', stepSize: 1 } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9CA3AF' } }
                }
            }
        });
    </script>
@endsection
