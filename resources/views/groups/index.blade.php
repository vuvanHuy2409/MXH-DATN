@extends('layouts.app')

@section('content')
<style>
    .container {
        max-width: 1100px !important;
    }

    .group-layout {
        display: flex;
        height: calc(100vh - 60px);
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        overflow: hidden;
        margin: 10px 0;
        box-shadow: var(--glass-shadow);
    }

    .group-sidebar {
        width: 320px;
        border-right: 1.5px solid var(--glass-border);
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.03);
    }

    .group-main {
        flex-grow: 1;
        overflow-y: auto;
        background: transparent;
        display: flex;
        flex-direction: column;
    }

    .group-item-sidebar {
        padding: 15px 20px;
        display: flex;
        gap: 12px;
        align-items: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }

    .group-item-sidebar:hover {
        background: rgba(0, 0, 0, 0.03);
    }

    .group-item-sidebar.active {
        background: rgba(0, 113, 227, 0.08);
        border-left-color: var(--accent-color);
    }

    .modern-input {
        width: 100%;
        padding: 12px 18px;
        border-radius: 14px;
        border: 2px solid #edf2f7;
        background: #ffffff !important;
        color: #000000 !important;
        outline: none;
        box-sizing: border-box;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: block;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        -webkit-text-fill-color: #000000 !important;
    }

    /* Đảm bảo text trong select và option luôn đen và rõ nét */
    select.modern-input, 
    select.modern-input option,
    #facultySelect,
    #classSelect,
    #facultySelect option,
    #classSelect option {
        color: #000000 !important;
        background-color: #ffffff !important;
        font-weight: 600 !important;
        -webkit-text-fill-color: #000000 !important;
    }

    input.modern-input:focus, select.modern-input:focus {
        border-color: var(--accent-color);
        background: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
    }

    input.modern-input, select.modern-input {
        height: 52px;
    }

    select.modern-input {
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%23000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 18px center !important;
        padding-right: 45px !important;
    }

    .input-label {
        display: block;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #000000 !important;
    }

    input.modern-input:disabled, select.modern-input:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        background: rgba(0, 0, 0, 0.05);
        color: #000000 !important;
        -webkit-text-fill-color: #000000;
    }

    select.modern-input option {
        color: #000000 !important;
        background: #ffffff !important;
    }

    @media (max-width: 900px) {
        .group-layout {
            height: auto;
            flex-direction: column;
            border-radius: 0;
            border: none;
            margin: 0;
        }

        .group-sidebar {
            width: 100%;
            border-right: none;
        }
    }
</style>

<div class="group-layout">
    <!-- Cột trái: Sidebar -->
    <div class="group-sidebar">
        <div style="padding: 25px 20px 15px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 800;">Cộng đồng</h2>
                <div style="display: flex; gap: 8px;">
                    <button onclick="openJoinByCodeModal()" style="background: rgba(0,0,0,0.05); border: none; padding: 8px; border-radius: 10px; cursor: pointer;" title="Nhập mã">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                    </button>
                    <button onclick="openCreateGroupModal()" style="background: var(--accent-color); border: none; color: white; padding: 8px; border-radius: 10px; cursor: pointer;" title="Tạo mới">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Search -->
            <form action="{{ route('groups.index') }}" method="GET" style="position: relative; margin-bottom: 10px;">
                <input type="text" name="q" value="{{ $query ?? '' }}" placeholder="Tìm kiếm..." style="width: 81.5%; padding: 10px 15px 10px 35px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-size: 14px; outline: none;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.5;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </form>
        </div>

        <div style="flex-grow: 1; overflow-y: auto;">
            <div style="padding: 5px 20px 5px; font-size: 11px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px;">Nhóm của bạn</div>
            @foreach($myGroups as $myG)
            <a href="{{ route('groups.index', $myG->slug) }}" class="group-item-sidebar {{ $activeGroup && $activeGroup->id === $myG->id ? 'active' : '' }}">
                <div class="avatar" style="width: 44px; height: 44px; background-image: url('{{ $myG->avatar_url }}'); background-size: cover; border-radius: 12px; flex-shrink: 0; border: 1px solid var(--glass-border);"></div>
                <div style="flex-grow: 1; overflow: hidden;">
                    <div style="font-weight: 700; font-size: 14.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $myG->name }}</div>
                    <div style="font-size: 12px; color: var(--secondary-text); margin-top: 2px;">{{ $myG->members_count }} thành viên</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Cột phải: Nội dung chính -->
    <div class="group-main">
        @if($activeGroup)
        <!-- CHI TIẾT NHÓM -->
        <!-- Header/Cover -->
        <div style="height: 200px; background-image: url('{{ asset('images/pngtree-diverse-group-of-people-standing-in-front-a-city-skyline-image_20636604.webp') }}'); background-size: cover; background-position: center; position: relative; flex-shrink: 0; border-bottom: 1px solid var(--glass-border);">
            <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%);"></div>
            
            <!-- More Button (Three Dots) -->
            <div style="position: absolute; top: 20px; right: 25px; z-index: 10;">
                <button onclick="toggleGroupMenu(event)" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); color: white; width: 38px; height: 38px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="groupManagementMenu" style="display: none; position: absolute; top: 45px; right: 0; width: 220px; background: var(--glass-bg); backdrop-filter: blur(30px); border: 1px solid var(--glass-border); border-radius: 18px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); overflow: hidden; animation: fadeIn 0.2s ease;">
                    @if(auth()->user()->id === $activeGroup->creator_id || (isset($isMember) && $activeGroup->members()->where('user_id', auth()->id())->first()->role === 'admin'))
                    <div onclick="editGroupInfo()" style="padding: 12px 18px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s; font-size: 14px; font-weight: 600;" onmouseover="this.style.background='rgba(0,113,227,0.1)'; this.style.color='var(--accent-color)'" onmouseout="this.style.background='transparent'; this.style.color='inherit'">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1h1l9.5-9.5z"></path></svg>
                        Chỉnh sửa thông tin
                    </div>
                    @endif
                    <div onclick="openMembersModal()" style="padding: 12px 18px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s; font-size: 14px; font-weight: 600; border-top: 1px solid var(--glass-border);" onmouseover="this.style.background='rgba(0,113,227,0.1)'; this.style.color='var(--accent-color)'" onmouseout="this.style.background='transparent'; this.style.color='inherit'">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        Thành viên nhóm
                    </div>
                    <div onclick="copyJoinCode('{{ $activeGroup->join_code }}')" style="padding: 12px 18px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s; font-size: 14px; font-weight: 600; border-top: 1px solid var(--glass-border);" onmouseover="this.style.background='rgba(0,113,227,0.1)'; this.style.color='var(--accent-color)'" onmouseout="this.style.background='transparent'; this.style.color='inherit'">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        Mã nhóm: <span style="font-family: monospace; letter-spacing: 1px; margin-left: auto;">{{ $activeGroup->join_code }}</span>
                    </div>
                    @if(auth()->id() !== $activeGroup->creator_id)
                    <div onclick="openReportModal('group', {{ $activeGroup->id }}, '{{ $activeGroup->name }}')" style="padding: 12px 18px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: all 0.2s; font-size: 14px; font-weight: 600; border-top: 1px solid var(--glass-border); color: #ff3b30;" onmouseover="this.style.background='rgba(255,59,48,0.1)'" onmouseout="this.style.background='transparent'">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        Báo cáo cộng đồng
                    </div>
                    @endif
                </div>
            </div>

            <div style="position: absolute; bottom: -30px; left: 30px; display: flex; align-items: flex-end; gap: 20px;">
                <div class="avatar" style="width: 90px; height: 90px; background-image: url('{{ $activeGroup->avatar_url }}'); background-size: cover; border-radius: 24px; border: 5px solid var(--bg-main); box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 5; position: relative;">
                    @if(auth()->id() === $activeGroup->creator_id)
                    <div onclick="document.getElementById('groupAvatarInput').click()" 
                         style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; cursor: pointer; border-radius: 19px;" 
                         onmouseover="this.style.opacity='1'" 
                         onmouseout="this.style.opacity='0'">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="white" stroke-width="2.5" fill="none">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </div>
                    <form id="groupAvatarForm" action="{{ route('groups.update_avatar', $activeGroup->slug) }}" method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="file" name="avatar" id="groupAvatarInput" accept="image/*" onchange="document.getElementById('groupAvatarForm').submit()">
                    </form>
                    @endif
                </div>
                <div style="padding-bottom: 8px; z-index: 5;">
                    <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: white; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">{{ $activeGroup->name }}</h1>
                    <div style="display: flex; align-items: center; gap: 10px; color: #00d2ff; font-size: 13px; font-weight: 700; margin-top: 4px;">
                        <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 6px; backdrop-filter: blur(5px);">{{ $activeGroup->privacy === 'public' ? 'Công khai' : 'Riêng tư' }}</span>
                        <span>•</span>
                        <span>{{ $activeGroup->members_count }} thành viên</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 45px; padding: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <p style="margin: 0; font-size: 14px; color: var(--secondary-text); max-width: 70%;">{{ $activeGroup->description ?? 'Chào mừng bạn đến với cộng đồng.' }}</p>
                @if(!$isMember)
                <form action="{{ route('groups.join', $activeGroup->slug) }}" method="POST">@csrf<button type="submit" class="btn-post" style="padding: 7px 20px; border-radius: 10px; font-size: 12px;">Tham gia</button></form>
                @endif
            </div>

            @if($isMember)
            <!-- Create Post Trigger (Modern Style) -->
            <div class="glass-bubble" style="margin-bottom: 30px; padding: 15px 25px; cursor: pointer; display: flex; align-items: center; gap: 15px; border-radius: 24px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onclick="openGroupPostModal('{{ $activeGroup->id }}', '{{ $activeGroup->name }}', '{{ $activeGroup->avatar_url }}')" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--glass-shadow)'">
                <div class="avatar" style="background-image: url('{{ auth()->user()->avatar_url }}'); background-size: cover; width: 44px; height: 44px; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 14px !important;"></div>
                <div style="color: var(--secondary-text); font-size: 15px; font-weight: 500; flex-grow: 1; opacity: 0.7;">Chia sẻ điều gì đó với nhóm {{ $activeGroup->name }}...</div>
                <div style="background: var(--accent-color); color: white; padding: 8px 20px; border-radius: 14px; font-weight: 800; font-size: 14px; box-shadow: 0 8px 20px rgba(0, 113, 227, 0.2);">Đăng bài</div>
            </div>
            @elseif($activeGroup->privacy === 'private')
            <div style="text-align: center; padding: 40px 20px; background: rgba(0,0,0,0.02); border-radius: 20px; margin-bottom: 20px;">
                <h3 style="margin: 0; font-size: 16px; font-weight: 800;">Nhóm riêng tư</h3>
                <p style="color: var(--secondary-text); margin-top: 5px; font-size: 13px;">Hãy tham gia để xem nội dung.</p>
            </div>
            @endif

            @if($activeGroup->privacy === 'public' || $isMember)
            <div id="group-posts">
                @forelse($posts as $post) @include('posts._item', ['post' => $post, 'prefix' => 'grp']) @empty
                <div style="text-align: center; padding: 50px 20px; opacity: 0.5; font-size: 14px;">Chưa có thảo luận nào.</div>
                @endforelse
            </div>
            @endif
        </div>
        @else
        <!-- TRANG TRỐNG (KHI CHƯA THAM GIA NHÓM NÀO) -->
        <div style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center;">
            <div style="width: 100px; height: 100px; background: rgba(0,113,227,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; color: var(--accent-color);">
                <svg viewBox="0 0 24 24" width="50" height="50" stroke="currentColor" stroke-width="1.5" fill="none">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <h3 style="margin: 0; font-size: 20px; font-weight: 800;">Bạn chưa tham gia nhóm nào</h3>
            <p style="margin: 10px 0 25px; color: var(--secondary-text); max-width: 300px; line-height: 1.5;">Hãy tạo nhóm mới hoặc nhập mã để tham gia vào cộng đồng của bạn.</p>
            <div style="display: flex; gap: 10px;">
                <button onclick="openJoinByCodeModal()" style="background: var(--glass-bg); border: 1px solid var(--glass-border); padding: 12px 25px; border-radius: 20px; font-weight: 700; cursor: pointer;">Nhập mã</button>
                <button onclick="openCreateGroupModal()" style="background: var(--accent-color); color: white; border: none; padding: 12px 25px; border-radius: 20px; font-weight: 700; cursor: pointer;">Tạo nhóm mới</button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modals (Tạo nhóm, Nhập mã) -->
<div id="joinByCodeModal" class="modal" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); backdrop-filter: blur(15px); z-index: 5000;">
    <div class="modal-content glass-bubble" style="max-width: 400px; padding: 35px; border-radius: 35px; width: 90%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 style="margin: 0; font-size: 20px; font-weight: 800;">Gia nhập bằng mã</h3>
            <span onclick="closeJoinByCodeModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
        </div>
        <form action="{{ route('groups.join_by_code') }}" method="POST">
            @csrf
            <input type="text" name="code" maxlength="5" required style="width: 100%; padding: 15px; border-radius: 15px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-size: 28px; font-weight: 800; text-align: center; text-transform: uppercase; letter-spacing: 10px;" placeholder="XXXXX">
            <button type="submit" class="btn-post" style="width: 100%; padding: 15px; border-radius: 15px; font-weight: 700; margin-top: 20px;">Xác nhận tham gia</button>
        </form>
    </div>
</div>

<div id="createGroupModal" class="modal" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); backdrop-filter: blur(15px); z-index: 5000;">
    <div class="modal-content glass-bubble" style="max-width: 500px; padding: 35px; border-radius: 35px; width: 90%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 id="groupModalTitle" style="margin: 0; font-size: 22px; font-weight: 800;">Tạo cộng đồng mới</h3>
            <span onclick="closeCreateGroupModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
        </div>
        <form id="groupForm" action="{{ route('groups.store') }}" method="POST">
            @csrf
            <div id="groupTypeSelection" style="margin-bottom: 20px; display: flex; gap: 10px;">
                <div onclick="setGroupType('community')" id="type-community" style="flex: 1; padding: 12px; border-radius: 15px; border: 2px solid var(--accent-color); cursor: pointer; text-align: center; background: rgba(0,113,227,0.05);">
                    <div style="font-weight: 800; font-size: 14px; color: var(--accent-color);">Cộng đồng</div>
                </div>
                @if(auth()->user()->user_type === 'teacher')
                <div onclick="setGroupType('class')" id="type-class" style="flex: 1; padding: 12px; border-radius: 15px; border: 2px solid var(--glass-border); cursor: pointer; text-align: center;">
                    <div style="font-weight: 800; font-size: 14px; color: var(--secondary-text);">Nhóm lớp</div>
                </div>
                @endif
                <input type="hidden" name="type" id="groupTypeInput" value="community">
            </div>
            <div style="margin-bottom: 15px;"><label class="input-label">Tên nhóm</label><input type="text" name="name" id="groupNameInput" required class="modern-input" placeholder="Tên nhóm..."></div>
            <div id="classSelectionField" style="display: none; margin-bottom: 15px;">
                <label class="input-label">Chọn Khoa</label>
                <select id="facultySelect" class="modern-input" onchange="filterClassesByFaculty(this.value)" style="margin-bottom: 15px;">
                    <option value="">-- Chọn khoa --</option>
                    @foreach($faculties as $f)
                    <option value="{{ $f->id }}">{{ $f->faculty_name }}</option>
                    @endforeach
                </select>

                <label class="input-label">Chọn Lớp học</label>
                <select name="class_name" id="classSelect" class="modern-input" disabled>
                    <option value="">-- Vui lòng chọn khoa trước --</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;"><label class="input-label">Mô tả</label><textarea name="description" id="groupDescInput" class="modern-input" style="min-height: 80px; resize: none;"></textarea></div>
            <div id="privacyField" style="margin-bottom: 25px;"><label class="input-label">Quyền riêng tư</label><select name="privacy" id="groupPrivacyInput" class="modern-input">
                    <option value="public">Công khai</option>
                    <option value="private">Riêng tư</option>
                </select></div>
            <button type="submit" id="groupSubmitBtn" class="btn-post" style="width: 100%; padding: 15px; border-radius: 15px; font-weight: 700;">Hoàn tất tạo nhóm</button>
        </form>

        @if(isset($activeGroup) && auth()->id() === $activeGroup->creator_id)
        <div id="deleteGroupSection" style="display: none; margin-top: 25px; padding-top: 25px; border-top: 1px solid var(--glass-border);">
            <div style="background: rgba(255,59,48,0.05); padding: 20px; border-radius: 20px; border: 1px solid rgba(255,59,48,0.1);">
                <h4 style="margin: 0 0 10px; color: #ff3b30; font-size: 15px; font-weight: 800;">Vùng nguy hiểm</h4>
                <p style="margin: 0 0 15px; font-size: 12px; color: var(--secondary-text); line-height: 1.5;">Xóa nhóm sẽ xóa vĩnh viễn tất cả bài viết, thành viên và dữ liệu liên quan. Hành động này không thể hoàn tác.</p>
                <form action="{{ route('groups.destroy', $activeGroup->slug) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN nhóm này không?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 100%; padding: 12px; border-radius: 12px; background: #ff3b30; color: white; border: none; font-weight: 800; cursor: pointer; transition: all 0.2s; font-size: 13px;">Xóa nhóm vĩnh viễn</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Members Management Modal -->
<div id="membersModal" class="modal" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); backdrop-filter: blur(15px); z-index: 5000;">
    <div class="modal-content glass-bubble" style="max-width: 500px; padding: 0; border-radius: 32px; width: 95%; overflow: hidden; height: 80vh; display: flex; flex-direction: column;">
        <!-- Modal Header -->
        <div style="padding: 20px 25px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.05);">
            <h3 style="margin: 0; font-size: 18px; font-weight: 800;">Thành viên nhóm</h3>
            <span onclick="closeMembersModal()" style="cursor: pointer; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; font-size: 20px; opacity: 0.6;">&times;</span>
        </div>

        <!-- Search & Add Section -->
        <div style="padding: 20px 25px; background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <div style="position: relative; margin-bottom: 12px;">
                <input type="text" id="memberSearchInput" oninput="searchMembersInModal()" placeholder="Tìm kiếm hoặc thêm thành viên..." style="width: 100%; padding: 12px 15px 12px 40px; border-radius: 14px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-size: 14px; outline: none; font-weight: 600;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); opacity: 0.4;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div style="display: flex; gap: 10px;">
                <button onclick="switchMemberTab('list')" id="tab-member-list" class="mini-tab-active">Danh sách</button>
                <button onclick="switchMemberTab('add')" id="tab-member-add" class="mini-tab">Thêm mới</button>
            </div>
        </div>

        <!-- Members List Container -->
        <div id="membersListContainer" style="flex-grow: 1; overflow-y: auto; padding: 10px 0;">
            <!-- Members will be loaded here via JS -->
            <div style="padding: 40px; text-align: center; opacity: 0.5;">Đang tải danh sách...</div>
        </div>

        <!-- Modal Footer (Leave Group) -->
        @if(isset($activeGroup) && $isMember && auth()->id() !== $activeGroup->creator_id)
        <div style="padding: 15px 25px; border-top: 1px solid var(--glass-border); background: rgba(255,59,48,0.03);">
            <form action="{{ route('groups.leave', $activeGroup->slug) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn rời nhóm này?')">
                @csrf
                <button type="submit" style="width: 100%; padding: 12px; border-radius: 12px; background: none; border: 1.5px solid rgba(255,59,48,0.3); color: #ff3b30; font-weight: 800; cursor: pointer; transition: all 0.2s; font-size: 13px;" onmouseover="this.style.background='rgba(255,59,48,0.1)'" onmouseout="this.style.background='none'">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" style="vertical-align: middle; margin-right: 5px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Rời khỏi cộng đồng
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

<style>
    .mini-tab, .mini-tab-active {
        padding: 6px 15px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .mini-tab {
        background: rgba(0,0,0,0.03);
        color: var(--secondary-text);
    }
    .mini-tab-active {
        background: var(--accent-color);
        color: white;
    }
    .member-row {
        padding: 12px 25px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s;
    }
    .member-row:hover {
        background: rgba(0,0,0,0.02);
    }
</style>

<script>
    const studentClasses = @json($studentDetails);

    function filterClassesByFaculty(facultyId) {
        const classSelect = document.getElementById('classSelect');
        classSelect.innerHTML = '<option value="">-- Chọn lớp --</option>';

        if (!facultyId) {
            classSelect.disabled = true;
            return;
        }

        const filtered = studentClasses.filter(item => item.faculty_id == facultyId);

        if (filtered.length > 0) {
            filtered.forEach(item => {
                const option = document.createElement('option');
                option.value = item.class;
                option.textContent = item.class;
                classSelect.appendChild(option);
            });
            classSelect.disabled = false;
        } else {
            classSelect.innerHTML = '<option value="">-- Không có lớp nào thuộc khoa này --</option>';
            classSelect.disabled = true;
        }
    }

    function toggleGroupMenu(event) {
        event.stopPropagation();
        const menu = document.getElementById('groupManagementMenu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    // Close menu when clicking outside
    document.addEventListener('click', function() {
        const menu = document.getElementById('groupManagementMenu');
        if (menu) menu.style.display = 'none';
    });

    function editGroupInfo() {
        @if(isset($activeGroup))
        // Set modal to EDIT state
        document.getElementById('groupModalTitle').innerText = "Chỉnh sửa thông tin";
        document.getElementById('groupForm').action = "/groups/{{ $activeGroup->slug }}";
        
        // Add PUT method if not exists
        if (!document.getElementById('groupMethodPut')) {
            const methodInput = document.createElement('input');
            methodInput.id = "groupMethodPut";
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            document.getElementById('groupForm').appendChild(methodInput);
        }

        document.getElementById('groupNameInput').value = "{{ $activeGroup->name }}";
        document.getElementById('groupDescInput').value = "{{ $activeGroup->description }}";
        document.getElementById('groupPrivacyInput').value = "{{ $activeGroup->privacy }}";
        document.getElementById('groupSubmitBtn').innerText = "Lưu thay đổi";
        document.getElementById('groupTypeSelection').style.display = "none";

        const deleteSection = document.getElementById('deleteGroupSection');
        if (deleteSection) deleteSection.style.display = "block";

        document.getElementById('createGroupModal').style.display = 'flex';
        document.body.classList.add('modal-open');
        @endif
    }

    function viewMembers() {
        openMembersModal();
    }

    let activeMemberTab = 'list';
    let currentGroupSlug = '{{ $activeGroup->slug ?? "" }}';

    function openMembersModal() {
        document.getElementById('membersModal').style.display = 'flex';
        document.body.classList.add('modal-open');
        loadMembers();
    }

    function closeMembersModal() {
        document.getElementById('membersModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function switchMemberTab(tab) {
        activeMemberTab = tab;
        document.getElementById('tab-member-list').className = tab === 'list' ? 'mini-tab-active' : 'mini-tab';
        document.getElementById('tab-member-add').className = tab === 'add' ? 'mini-tab-active' : 'mini-tab';
        document.getElementById('memberSearchInput').placeholder = tab === 'list' ? 'Tìm trong nhóm...' : 'Tìm người dùng để thêm...';
        document.getElementById('memberSearchInput').value = '';
        if (tab === 'list') loadMembers();
        else document.getElementById('membersListContainer').innerHTML = '<div style="padding: 40px; text-align: center; opacity: 0.5;">Nhập tên người dùng để tìm kiếm...</div>';
    }

    function loadMembers() {
        const container = document.getElementById('membersListContainer');
        container.innerHTML = '<div style="padding: 40px; text-align: center; opacity: 0.5;">Đang tải...</div>';
        
        fetch(`/groups/${currentGroupSlug}/members`)
            .then(res => res.json())
            .then(members => {
                displayMembers(members);
            });
    }

    function displayMembers(members) {
        const container = document.getElementById('membersListContainer');
        if (members.length === 0) {
            container.innerHTML = '<div style="padding: 40px; text-align: center; opacity: 0.5;">Không tìm thấy thành viên nào.</div>';
            return;
        }

        container.innerHTML = members.map(m => `
            <div class="member-row">
                <div class="avatar" style="width: 40px; height: 40px; background-image: url('${m.avatar_url}'); background-size: cover; border-radius: 12px; border: 1px solid var(--glass-border);"></div>
                <div style="flex-grow: 1;">
                    <div style="font-weight: 700; font-size: 14px;">${m.username}</div>
                    <div style="font-size: 11px; color: var(--secondary-text); text-transform: uppercase;">${m.pivot.role}</div>
                </div>
                ${m.id !== {{ auth()->id() }} ? '<button style="background: none; border: none; color: var(--accent-color); font-weight: 700; font-size: 12px; cursor: pointer;">Xem hồ sơ</button>' : '<span style="font-size: 11px; opacity: 0.5;">Bạn</span>'}
            </div>
        `).join('');
    }

    function searchMembersInModal() {
        const q = document.getElementById('memberSearchInput').value.trim();
        const container = document.getElementById('membersListContainer');

        if (activeMemberTab === 'list') {
            // Client-side filter for current members (simple version)
            const rows = container.querySelectorAll('.member-row');
            rows.forEach(row => {
                const name = row.querySelector('div > div:first-child').innerText.toLowerCase();
                row.style.display = name.includes(q.toLowerCase()) ? 'flex' : 'none';
            });
        } else {
            if (q.length < 2) return;
            fetch(`/groups/${currentGroupSlug}/search-users?q=${q}`)
                .then(res => res.json())
                .then(users => {
                    if (users.length === 0) {
                        container.innerHTML = '<div style="padding: 40px; text-align: center; opacity: 0.5;">Không tìm thấy người dùng phù hợp.</div>';
                        return;
                    }
                    container.innerHTML = users.map(u => `
                        <div class="member-row">
                            <div class="avatar" style="width: 40px; height: 40px; background-image: url('${u.avatar_url}'); background-size: cover; border-radius: 12px; border: 1px solid var(--glass-border);"></div>
                            <div style="flex-grow: 1;">
                                <div style="font-weight: 700; font-size: 14px;">${u.username}</div>
                            </div>
                            <button onclick="addMemberToGroup(${u.id}, '${u.username}')" style="background: var(--accent-color); color: white; border: none; padding: 6px 15px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer;">Thêm</button>
                        </div>
                    `).join('');
                });
        }
    }

    function addMemberToGroup(userId, username) {
        if (!confirm(`Thêm ${username} vào nhóm?`)) return;

        fetch(`/groups/${currentGroupSlug}/add-member`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            switchMemberTab('list');
        });
    }

    function searchMembers() {
        openMembersModal();
        switchMemberTab('list');
        setTimeout(() => document.getElementById('memberSearchInput').focus(), 100);
    }

    function copyJoinCode(code) {
        navigator.clipboard.writeText(code);
        alert('Đã sao chép mã: ' + code);
    }

    function openCreateGroupModal() {
        // Reset modal to CREATE state
        document.getElementById('groupModalTitle').innerText = "Tạo cộng đồng mới";
        document.getElementById('groupForm').action = "{{ route('groups.store') }}";
        document.getElementById('groupMethodPut')?.remove();
        document.getElementById('groupNameInput').value = "";
        document.getElementById('groupDescInput').value = "";
        document.getElementById('groupPrivacyInput').value = "public";
        document.getElementById('groupSubmitBtn').innerText = "Hoàn tất tạo nhóm";
        document.getElementById('groupTypeSelection').style.display = "flex";
        
        const deleteSection = document.getElementById('deleteGroupSection');
        if (deleteSection) deleteSection.style.display = "none";

        document.getElementById('createGroupModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    }

    function closeCreateGroupModal() {
        document.getElementById('createGroupModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function openJoinByCodeModal() {
        document.getElementById('joinByCodeModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    }

    function closeJoinByCodeModal() {
        document.getElementById('joinByCodeModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function setGroupType(type) {
        const communityBtn = document.getElementById('type-community'),
            classBtn = document.getElementById('type-class'),
            typeInput = document.getElementById('groupTypeInput');
        const classField = document.getElementById('classSelectionField'),
            privacyField = document.getElementById('privacyField');
        
        const communityText = communityBtn.querySelector('div');
        const classText = classBtn.querySelector('div');

        typeInput.value = type;
        if (type === 'community') {
            communityBtn.style.borderColor = 'var(--accent-color)';
            communityBtn.style.background = 'rgba(0,113,227,0.05)';
            communityText.style.color = 'var(--accent-color)';
            
            classBtn.style.borderColor = 'var(--glass-border)';
            classBtn.style.background = 'transparent';
            classText.style.color = 'var(--secondary-text)';
            
            classField.style.display = 'none';
            privacyField.style.display = 'block';
        } else {
            classBtn.style.borderColor = 'var(--accent-color)';
            classBtn.style.background = 'rgba(0,113,227,0.05)';
            classText.style.color = '#000000';
            
            communityBtn.style.borderColor = 'var(--glass-border)';
            communityBtn.style.background = 'transparent';
            communityText.style.color = 'var(--secondary-text)';
            
            classField.style.display = 'block';
            privacyField.style.display = 'none';
        }
    }

    function openGroupPostModal(id, name, avatarUrl) {
        openModal();
        const modal = document.getElementById('postModal');
        const form = modal.querySelector('form');
        
        if (form) {
            // Handle group_id input
            let groupInput = form.querySelector('input[name="group_id"]');
            if (!groupInput) {
                groupInput = document.createElement('input');
                groupInput.type = 'hidden';
                groupInput.name = 'group_id';
                form.appendChild(groupInput);
            }
            groupInput.value = id;

            // Update Modal Header & Context
            const modalTitle = document.getElementById('modalMainTitle');
            if (modalTitle) modalTitle.innerText = "Đăng vào nhóm";

            const postStatus = document.getElementById('modalPostStatus');
            if (postStatus) {
                postStatus.innerHTML = `<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg> <span>Đang đăng vào: <strong>${name}</strong></span>`;
                postStatus.style.color = 'var(--accent-color)';
            }

            // Show mini group avatar
            const miniAvatar = document.getElementById('groupMiniAvatar');
            if (miniAvatar && avatarUrl) {
                miniAvatar.style.backgroundImage = `url('${avatarUrl}')`;
                miniAvatar.style.display = 'block';
            }

            const textarea = form.querySelector('textarea');
            if (textarea) {
                textarea.placeholder = "Bạn muốn chia sẻ điều gì với nhóm " + name + "?";
                textarea.focus();
            }
        }
    }

    // Enhance the reset logic
    const originalCloseModal = window.closeModal;
    window.closeModal = function() {
        if (originalCloseModal) originalCloseModal();
        
        const modal = document.getElementById('postModal');
        if (modal) {
            const form = modal.querySelector('form');
            if (form) {
                const groupInput = form.querySelector('input[name="group_id"]');
                if (groupInput) groupInput.remove();
                
                const miniAvatar = document.getElementById('groupMiniAvatar');
                if (miniAvatar) miniAvatar.style.display = 'none';

                const modalTitle = document.getElementById('modalMainTitle');
                if (modalTitle) modalTitle.innerText = "Tạo bài viết";

                const postStatus = document.getElementById('modalPostStatus');
                if (postStatus) {
                    postStatus.innerHTML = `<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg> Công khai`;
                    postStatus.style.color = 'var(--secondary-text)';
                }
                
                const textarea = form.querySelector('textarea');
                if (textarea) textarea.placeholder = "Bạn đang nghĩ gì?";
            }
        }
    };

    let currentReport = { type: '', id: '' };

    function openReportModal(type, id, name) {
        currentReport = { type, id };
        document.getElementById('reportTargetType').innerText = type === 'group' ? 'Cộng đồng' : 'Nội dung';
        document.getElementById('reportTargetName').innerText = name;
        document.getElementById('reportModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    }

    function closeReportModal() {
        document.getElementById('reportModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function submitReport() {
        const reason = document.getElementById('reportReason').value;
        const details = document.getElementById('reportDetails').value;
        const btn = document.getElementById('submitReportBtn');
        
        btn.disabled = true;
        btn.innerText = "Đang gửi...";

        fetch('{{ route('reports.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                reported_id: currentReport.id,
                type: currentReport.type,
                reason: reason,
                details: details
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            closeReportModal();
            btn.disabled = false;
            btn.innerText = "Gửi báo cáo";
        })
        .catch(err => {
            alert('Có lỗi xảy ra, vui lòng thử lại.');
            btn.disabled = false;
            btn.innerText = "Gửi báo cáo";
        });
    }
</script>

<!-- Modal Báo cáo -->
<div id="reportModal" class="modal" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.4); backdrop-filter: blur(10px); z-index: 6000;">
    <div class="modal-content glass-bubble" style="max-width: 450px; width: 90%; padding: 25px; border-radius: 25px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 800;">Báo cáo <span id="reportTargetType"></span></h3>
            <span onclick="closeReportModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
        </div>
        
        <div style="margin-bottom: 20px;">
            <p style="font-size: 14px; color: var(--secondary-text); margin-bottom: 15px;">Bạn đang báo cáo: <strong id="reportTargetName" style="color: var(--text-color);"></strong></p>
            
            <label class="input-label" style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px;">Lý do báo cáo</label>
            <select id="reportReason" style="width: 100%; margin-bottom: 15px; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); outline: none;">
                <option value="Spam">Spam / Nội dung rác</option>
                <option value="Quấy rối">Quấy rối / Bắt nạt</option>
                <option value="Nội dung không phù hợp">Nội dung không phù hợp</option>
                <option value="Ngôn từ thù ghét">Ngôn từ thù ghét</option>
                <option value="Vi phạm bản quyền">Vi phạm bản quyền</option>
                <option value="Khác">Lý do khác</option>
            </select>
            
            <label class="input-label" style="display: block; font-size: 14px; font-weight: 700; margin-bottom: 8px;">Chi tiết thêm (tùy chọn)</label>
            <textarea id="reportDetails" style="width: 100%; height: 100px; resize: none; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); outline: none;" placeholder="Cung cấp thêm thông tin để chúng tôi xử lý nhanh hơn..."></textarea>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button onclick="closeReportModal()" style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: transparent; font-weight: 700; cursor: pointer;">Hủy</button>
            <button onclick="submitReport()" id="submitReportBtn" style="flex: 2; padding: 12px; border-radius: 12px; border: none; background: #ff3b30; color: white; font-weight: 700; cursor: pointer;">Gửi báo cáo</button>
        </div>
    </div>
</div>
@endsection