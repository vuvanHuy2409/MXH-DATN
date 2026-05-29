<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - MXH-DATN Admin</title>
    <!-- Tailwind CSS 4.0 -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #F5F6FA;
        }
        .sidebar {
            width: 220px;
            border-right: 1px solid #E5E7EB;
        }
        .main-content {
            width: calc(100% - 220px);
        }
        .active-menu-item {
            background-color: #EEF2FF;
            color: #3B5BDB;
            font-weight: 600;
        }
        .active-menu-item i {
            color: #3B5BDB;
        }
        .stat-card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
    </style>
    @yield('styles')
</head>
<body class="flex min-h-screen">
    @if(isset($isToxicApiAvailable) && !$isToxicApiAvailable)
    <div id="toxic-api-banner-admin" style="position: fixed; top: 10px; left: 10px; background: rgba(220, 38, 38, 0.95); color: white; padding: 8px 15px; border-radius: 10px; font-size: 11px; font-weight: 800; z-index: 9999; backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 4px 20px rgba(220, 38, 38, 0.4); display: flex; align-items: center; gap: 8px; pointer-events: auto;">
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="display: inline-block; width: 8px; height: 8px; background: #fff; border-radius: 50%; animation: admin-offline-pulse 0.8s infinite alternate;"></span>
            API KIỂM DUYỆT: NGOẠI TUYẾN
        </div>
        <div onclick="this.parentElement.style.display='none'" style="cursor: pointer; opacity: 0.7; font-size: 14px; line-height: 1; padding: 2px;">&times;</div>
    </div>
    <style>
        @keyframes admin-offline-pulse {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0.5; transform: scale(1.2); }
        }
    </style>
    @endif

    <!-- SIDEBAR -->
    <aside class="sidebar fixed h-screen bg-white flex flex-col z-10">
        <!-- Header Sidebar -->
        <div class="p-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-[15px] font-bold text-black uppercase tracking-tight">MXH-DATN</span>
                <span class="bg-[#3B5BDB] text-white text-[10px] font-bold px-1.5 py-0.5 rounded-[4px]">ADMIN</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 overflow-y-auto">
            <!-- Group: TỔNG QUAN -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-[#9CA3AF] uppercase tracking-[0.8px] mb-2 px-2">TỔNG QUAN</p>
                <a href="{{ route('admin.dashboard') }}" class="{{ Route::is('admin.dashboard') ? 'active-menu-item' : 'text-[#374151]' }} flex items-center gap-2 text-[13px] p-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Group: NỘI DUNG -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-[#9CA3AF] uppercase tracking-[0.8px] mb-2 px-2">NỘI DUNG</p>
                <div class="flex flex-col gap-1">
                    <a href="{{ route('admin.reports.index') }}" class="{{ Route::is('admin.reports.*') ? 'active-menu-item' : 'text-[#374151]' }} flex items-center gap-2 text-[13px] p-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>Bài viết / Báo cáo vi phạm</span>
                    </a>
                </div>
            </div>

            <!-- Group: NGƯỜI DÙNG -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-[#9CA3AF] uppercase tracking-[0.8px] mb-2 px-2">NGƯỜI DÙNG</p>
                <div class="flex flex-col gap-1">
                    <a href="{{ route('admin.users.index') }}" class="{{ Route::is('admin.users.index') ? 'active-menu-item' : 'text-[#374151]' }} flex items-center gap-2 text-[13px] p-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="users" class="w-4 h-4"></i>
                        <span>Tài khoản</span>
                    </a>
                    <a href="{{ route('admin.users.flagged') }}" class="{{ Route::is('admin.users.flagged') ? 'active-menu-item' : 'text-[#374151]' }} flex items-center gap-2 text-[13px] p-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="user-x" class="w-4 h-4"></i>
                        <span>Tài khoản bị đánh dấu</span>
                    </a>
                    <a href="{{ route('admin.users.import.index') }}" class="{{ Route::is('admin.users.import.*') ? 'active-menu-item' : 'text-[#374151]' }} flex items-center gap-2 text-[13px] p-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                        <i data-lucide="file-up" class="w-4 h-4"></i>
                        <span>Import tài khoản</span>
                    </a>
                </div>
            </div>

            <!-- Group: HỆ THỐNG -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-[#9CA3AF] uppercase tracking-[0.8px] mb-2 px-2">HỆ THỐNG</p>
                <a href="{{ route('admin.settings.index') }}" class="{{ Route::is('admin.settings.*') ? 'active-menu-item' : 'text-[#374151]' }} flex items-center gap-2 text-[13px] p-2 px-4 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>Cài đặt hệ thống</span>
                </a>
            </div>
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/30">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#7C3AED] to-purple-400 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                    {{ substr(auth()->user()->username, 0, 1) }}
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-[#111827]">{{ auth()->user()->username }}</p>
                    <p class="text-[11px] text-[#6B7280]">Giảng viên / Quản trị viên</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <a href="/" class="flex items-center justify-center gap-1.5 text-[11px] font-medium py-1.5 px-2 border border-gray-200 rounded-md bg-white hover:bg-gray-50 transition-colors shadow-sm">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    Trang chủ
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-1.5 text-[11px] font-medium py-1.5 px-2 border border-gray-200 rounded-md bg-white hover:bg-red-50 hover:text-red-600 transition-colors shadow-sm">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content ml-[220px] flex-1 flex flex-col min-h-screen">
        
        <!-- TOPBAR -->
        <header class="h-14 bg-[#F5F6FA] flex items-center justify-between px-8">
            <nav class="flex items-center text-[13px]">
                <span class="text-[#6B7280]">Admin</span>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-[#111827] font-semibold">@yield('breadcrumb')</span>
            </nav>
            <div class="flex items-center gap-4">
                <span class="text-[12px] text-[#6B7280]">{{ now()->format('d/m/Y H:i') }}</span>
                <button class="p-1 text-[#9CA3AF] hover:text-[#374151] transition-colors">
                    <i data-lucide="settings" class="w-[18px] h-[18px]"></i>
                </button>
            </div>
        </header>

        <!-- CONTENT -->
        <div class="p-8 pt-2">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-100 text-green-600 rounded-xl text-[13px] flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl text-[13px] flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
