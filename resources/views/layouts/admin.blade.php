<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - MXH-DATN Admin</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ── CSS Variables: Light / Dark ── */
        :root {
            --bg:          #F5F6FA;
            --surface:     #ffffff;
            --surface-2:   #F9FAFB;
            --border:      #E5E7EB;
            --text-primary:#111827;
            --text-muted:  #6B7280;
            --text-faint:  #9CA3AF;
            --accent:      #3B5BDB;
            --accent-bg:   #EEF2FF;
            --sidebar-w:   220px;
            --danger:      #EF4444;
        }
        [data-theme="dark"] {
            --bg:          #0f0f11;
            --surface:     #1c1c1e;
            --surface-2:   #2c2c2e;
            --border:      rgba(255,255,255,0.08);
            --text-primary:#f5f5f7;
            --text-muted:  #ababab;
            --text-faint:  #636366;
            --accent:      #4f7aff;
            --accent-bg:   rgba(79,122,255,0.12);
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            margin: 0;
            transition: background 0.25s, color 0.25s;
        }

        /* ── Sidebar ── */
        .adm-sidebar {
            width: var(--sidebar-w);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 10;
            transition: background 0.25s, border-color 0.25s;
        }
        .adm-main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Nav items ── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            padding: 8px 14px;
            border-radius: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
        }
        .nav-item:hover {
            background: var(--surface-2);
            color: var(--text-primary);
        }
        .nav-item.active {
            background: var(--accent-bg);
            color: var(--accent);
            font-weight: 700;
        }
        .nav-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.7;
            flex-shrink: 0;
        }
        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-faint);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 0 14px;
            margin-bottom: 6px;
            margin-top: 4px;
        }

        /* ── Topbar ── */
        .adm-topbar {
            height: 52px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            transition: background 0.25s, border-color 0.25s;
        }

        /* ── Theme toggle ── */
        .theme-toggle {
            width: 36px; height: 20px;
            border-radius: 10px;
            background: var(--border);
            position: relative;
            cursor: pointer;
            border: 1.5px solid var(--border);
            transition: background 0.3s;
            flex-shrink: 0;
        }
        .theme-toggle::after {
            content: '';
            position: absolute;
            width: 14px; height: 14px;
            border-radius: 50%;
            background: var(--text-muted);
            top: 1px; left: 1px;
            transition: transform 0.3s, background 0.3s;
        }
        [data-theme="dark"] .theme-toggle {
            background: var(--accent);
        }
        [data-theme="dark"] .theme-toggle::after {
            transform: translateX(16px);
            background: white;
        }

        /* ── Cards ── */
        .adm-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: background 0.25s, border-color 0.25s;
        }

        /* ── Table ── */
        .adm-table thead th {
            background: var(--surface-2);
            color: var(--text-faint);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
        }
        .adm-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }
        .adm-table tbody tr:hover {
            background: var(--surface-2);
        }
        .adm-table tbody td {
            padding: 14px 20px;
            font-size: 13px;
            color: var(--text-primary);
            vertical-align: middle;
        }
        .adm-table tbody tr:last-child {
            border-bottom: none;
        }

        /* ── Badges ── */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }

        /* ── Buttons ── */
        .btn-danger {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 8px;
            font-size: 12px; font-weight: 600;
            color: var(--danger); background: rgba(239,68,68,0.08);
            border: none; cursor: pointer; transition: background 0.15s;
            font-family: inherit;
        }
        .btn-danger:hover { background: rgba(239,68,68,0.15); }
        .btn-muted {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 8px;
            font-size: 12px; font-weight: 600;
            color: var(--text-muted); background: var(--surface-2);
            border: none; cursor: pointer; transition: background 0.15s;
            font-family: inherit;
        }
        .btn-muted:hover { background: var(--border); }

        /* ── Input / Select ── */
        .adm-input {
            font-size: 13px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 7px 12px;
            background: var(--surface-2);
            color: var(--text-primary);
            outline: none;
            font-family: inherit;
            transition: border-color 0.2s, background 0.2s;
        }
        .adm-input:focus {
            border-color: var(--accent);
            background: var(--surface);
        }

        /* ── Admin offline banner ── */
        @keyframes adm-pulse {
            from { opacity: 1; transform: scale(1); }
            to   { opacity: 0.5; transform: scale(1.2); }
        }
    </style>
    @yield('styles')
</head>
<body>
    @if(isset($isToxicApiAvailable) && !$isToxicApiAvailable)
    <div id="adm-toxic-api-banner" style="position: fixed; top: 15px; left: 15px; background: #ffffff; color: #1f2937; padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 500; z-index: 99999; border: 1.5px solid #ef4444; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.15); display: flex; align-items: center; gap: 10px; max-width: 320px; line-height: 1.4; font-family: system-ui, -apple-system, sans-serif; animation: adm-slide-down 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; transition: opacity 0.5s ease, transform 0.5s ease;">
        <span style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; display: inline-block; animation: adm-pulse 0.8s infinite alternate; flex-shrink: 0;"></span>
        <span style="flex-grow: 1;">Hệ thống kiểm duyệt tự động hiện đang tạm đóng. Vui lòng chờ quản trị viên kích hoạt lại.</span>
        <button onclick="closeAdmToxicBanner()" style="background: transparent; border: none; color: #9ca3af; cursor: pointer; font-size: 16px; padding: 0 2px; font-weight: bold; flex-shrink: 0; line-height: 1;" onmouseover="this.style.color='#4b5563'" onmouseout="this.style.color='#9ca3af'">&times;</button>
    </div>
    <style>
        @keyframes adm-slide-down {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
    <script>
        function closeAdmToxicBanner() {
            const banner = document.getElementById('adm-toxic-api-banner');
            if (banner) {
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    banner.style.display = 'none';
                }, 500);
            }
        }
        setTimeout(closeAdmToxicBanner, 10000);
    </script>
    @endif

    <!-- SIDEBAR -->
    <aside class="adm-sidebar">
        <div style="padding: 20px 18px 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 15px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.5px;">E-Connect</span>
                <span style="background: var(--accent); color: white; font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 4px; letter-spacing: 0.5px;">ADMIN</span>
            </div>
        </div>

        <nav style="flex: 1; padding: 16px 10px; overflow-y: auto; display: flex; flex-direction: column; gap: 18px;">
            <div>
                <p class="nav-section-label">Tổng quan</p>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>
                </div>
            </div>
            <div>
                <p class="nav-section-label">Nội dung</p>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <a href="{{ route('admin.reports.index') }}" class="nav-item {{ Route::is('admin.reports.*') ? 'active' : '' }}">
                        Báo cáo vi phạm
                    </a>
                </div>
            </div>
            <div>
                <p class="nav-section-label">Người dùng</p>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <a href="{{ route('admin.users.index') }}" class="nav-item {{ Route::is('admin.users.index') ? 'active' : '' }}">
                        Tài khoản
                    </a>
                    <a href="{{ route('admin.users.flagged') }}" class="nav-item {{ Route::is('admin.users.flagged') ? 'active' : '' }}">
                        Tài khoản bị đánh dấu
                    </a>
                    <a href="{{ route('admin.users.import.index') }}" class="nav-item {{ Route::is('admin.users.import.*') ? 'active' : '' }}">
                        Import tài khoản
                    </a>
                </div>
            </div>
            <div>
                <p class="nav-section-label">Hệ thống</p>
                <div style="display: flex; flex-direction: column; gap: 2px;">
                    <a href="{{ route('admin.settings.index') }}" class="nav-item {{ Route::is('admin.settings.*') ? 'active' : '' }}">
                        Cài đặt
                    </a>
                </div>
            </div>
        </nav>

        <div style="padding: 14px 16px; border-top: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #7C3AED, #a855f7); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 13px; flex-shrink: 0;">
                    {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                </div>
                <div style="min-width: 0;">
                    <p style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->username }}</p>
                    <p style="font-size: 11px; color: var(--text-faint); margin: 0;">Quản trị viên</p>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">
                <a href="/" style="display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; padding: 7px; border-radius: 8px; border: 1.5px solid var(--border); color: var(--text-muted); text-decoration: none; transition: background 0.15s;" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'">Trang chủ</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; padding: 7px; border-radius: 8px; border: 1.5px solid var(--border); color: var(--text-muted); background: transparent; cursor: pointer; transition: background 0.15s, color 0.15s; font-family: inherit;" onmouseover="this.style.background='rgba(239,68,68,0.08)';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='var(--text-muted)'">Đăng xuất</button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="adm-main">
        <!-- TOPBAR -->
        <header class="adm-topbar">
            <nav style="display: flex; align-items: center; font-size: 13px; gap: 6px;">
                <span style="color: var(--text-faint);">Admin</span>
                <span style="color: var(--border);">/</span>
                <span style="color: var(--text-primary); font-weight: 600;">@yield('breadcrumb')</span>
            </nav>
            <div style="display: flex; align-items: center; gap: 14px;">
                <span style="font-size: 12px; color: var(--text-faint);">{{ now()->format('d/m/Y H:i') }}</span>
                <!-- Dark mode toggle -->
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; color: var(--text-faint); font-weight: 600;" id="themeLabel">Sáng</span>
                    <div class="theme-toggle" id="themeToggle" onclick="toggleAdminTheme()" title="Chuyển chế độ sáng/tối"></div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <div style="padding: 28px 28px 40px;">
            @if(session('success'))
                <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(34,197,94,0.08); border: 1.5px solid rgba(34,197,94,0.2); color: #16a34a; border-radius: 10px; font-size: 13px; font-weight: 600;">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(239,68,68,0.08); border: 1.5px solid rgba(239,68,68,0.2); color: #dc2626; border-radius: 10px; font-size: 13px; font-weight: 600;">
                    ✕ {!! session('error') !!}
                </div>
            @endif
            @if($errors->any())
                <div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(239,68,68,0.08); border: 1.5px solid rgba(239,68,68,0.2); color: #dc2626; border-radius: 10px; font-size: 13px; font-weight: 600;">
                    <ul style="list-style-type: none; padding-left: 0; margin: 0; display: flex; flex-direction: column; gap: 4px;">
                        @foreach($errors->all() as $error)
                            <li>✕ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <script>
    // Admin dark mode — persisted to localStorage
    (function() {
        const saved = localStorage.getItem('admin-theme') || 'light';
        document.documentElement.setAttribute('data-theme', saved);
        const label = document.getElementById('themeLabel');
        if (label) label.textContent = saved === 'dark' ? 'Tối' : 'Sáng';
    })();

    function toggleAdminTheme() {
        const html  = document.documentElement;
        const isDark = html.getAttribute('data-theme') === 'dark';
        const next  = isDark ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('admin-theme', next);
        const label = document.getElementById('themeLabel');
        if (label) label.textContent = next === 'dark' ? 'Tối' : 'Sáng';
    }
    </script>
    @yield('scripts')
</body>
</html>
