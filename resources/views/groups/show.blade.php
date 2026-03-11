@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1100px !important; }
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
        width: 350px;
        border-right: 1.5px solid var(--glass-border);
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.03);
    }
    .group-main {
        flex-grow: 1;
        overflow-y: auto;
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
    .group-item-sidebar:hover { background: rgba(0, 0, 0, 0.03); }
    .group-item-sidebar.active {
        background: rgba(0, 113, 227, 0.08);
        border-left-color: var(--accent-color);
    }
    @media (max-width: 900px) {
        .group-sidebar { display: none; }
        .container { max-width: 100% !important; }
    }
</style>

<div class="group-layout">
    <!-- Left Sidebar: My Groups -->
    <div class="group-sidebar">
        <div style="padding: 25px 20px 15px; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 800;">Cộng đồng</h2>
            <button onclick="window.location.href='{{ route('groups.index') }}'" style="background: none; border: none; cursor: pointer; color: var(--accent-color);">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
        </div>
        <div style="flex-grow: 1; overflow-y: auto;">
            @foreach($myGroups as $myG)
                <a href="{{ route('groups.show', $myG->slug) }}" class="group-item-sidebar {{ $myG->id === $group->id ? 'active' : '' }}">
                    <div class="avatar" style="width: 44px; height: 44px; background-image: url('{{ $myG->avatar_url }}'); background-size: cover; border-radius: 12px; flex-shrink: 0;"></div>
                    <div style="flex-grow: 1; overflow: hidden;">
                        <div style="font-weight: 700; font-size: 14.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $myG->name }}</div>
                        <div style="font-size: 12px; color: var(--secondary-text); margin-top: 2px;">{{ $myG->members_count }} thành viên</div>
                    </div>
                </a>
            @endforeach
        </div>
        <div style="padding: 20px; border-top: 1px solid var(--glass-border);">
            <button onclick="openCreateGroupModal()" class="btn-post" style="width: 100%; padding: 12px; border-radius: 12px; font-size: 14px;">+ Tạo nhóm mới</button>
        </div>
    </div>

    <!-- Right Main Content -->
    <div class="group-main">
        <!-- Group Cover -->
        <div style="height: 180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; flex-shrink: 0;">
            <div style="position: absolute; bottom: -30px; left: 25px; display: flex; align-items: flex-end; gap: 15px;">
                <div class="avatar" style="width: 80px; height: 80px; background-image: url('{{ $group->avatar_url }}'); background-size: cover; border-radius: 20px; border: 4px solid var(--bg-main); box-shadow: 0 8px 20px rgba(0,0,0,0.15);"></div>
                <div style="padding-bottom: 5px;">
                    <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.2);">{{ $group->name }}</h1>
                    <div style="display: flex; align-items: center; gap: 10px; color: #00d2ff; font-size: 13px; font-weight: 600; margin-top: 4px;">
                        <span>{{ $group->privacy === 'public' ? 'Công khai' : 'Riêng tư' }} • {{ $group->members_count }} thành viên</span>
                        <div style="display: flex; align-items: center; gap: 5px; background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 6px; cursor: pointer;" onclick="copyJoinCode('{{ $group->join_code }}')" title="Sao chép mã nhóm">
                            <span style="font-family: monospace; font-weight: 800;">#{{ $group->join_code }}</span>
                            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 40px; padding: 0 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <p style="margin: 0; font-size: 14px; color: var(--secondary-text); max-width: 70%;">{{ $group->description ?? 'Chào mừng bạn đến với cộng đồng.' }}</p>
                @if($isMember)
                    <form action="{{ route('groups.leave', $group->slug) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn rời nhóm?')">
                        @csrf
                        <button type="submit" style="background: none; border: 1px solid rgba(255,59,48,0.3); color: #ff3b30; padding: 8px 15px; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;">Rời nhóm</button>
                    </form>
                @else
                    <form action="{{ route('groups.join', $group->slug) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-post" style="padding: 8px 20px; border-radius: 10px; font-size: 13px;">Tham gia nhóm</button>
                    </form>
                @endif
            </div>

            @if($isMember)
                <div class="glass-bubble" style="margin-bottom: 25px; padding: 12px 20px; cursor: pointer; display: flex; align-items: center; gap: 12px; border-radius: 18px;" onclick="openGroupPostModal()">
                    <div class="avatar" style="background-image: url('{{ auth()->user()->avatar_url }}'); background-size: cover; width: 36px; height: 36px; border-radius: 50%;"></div>
                    <div style="color: var(--secondary-text); font-size: 14px; flex-grow: 1; opacity: 0.7;">Chia sẻ điều gì đó với nhóm...</div>
                    <div style="background: var(--accent-color); color: white; padding: 6px 15px; border-radius: 10px; font-weight: 700; font-size: 12px;">Đăng</div>
                </div>
            @elseif($group->privacy === 'private')
                <div style="text-align: center; padding: 40px 20px; background: rgba(0,0,0,0.02); border-radius: 20px; margin-bottom: 20px;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800;">Nhóm riêng tư</h3>
                    <p style="color: var(--secondary-text); margin-top: 5px; font-size: 13px;">Hãy tham gia để xem nội dung.</p>
                </div>
            @endif

            @if($group->privacy === 'public' || $isMember)
                <div id="group-posts" style="display: flex; flex-direction: column; gap: 0;">
                    @forelse($posts as $post)
                        @include('posts._item', ['post' => $post, 'prefix' => 'grp'])
                    @empty
                        <div style="text-align: center; padding: 50px 20px; opacity: 0.5; font-size: 14px;">Chưa có thảo luận nào.</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reuse Create Group Modal -->
<div id="createGroupModal" class="modal" style="display: none; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); backdrop-filter: blur(15px); z-index: 5000;">
    <div class="modal-content glass-bubble" style="max-width: 500px; padding: 30px; border-radius: 30px; width: 90%;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 style="margin: 0; font-size: 22px; font-weight: 800;">Tạo nhóm cộng đồng</h3>
            <span onclick="closeCreateGroupModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
        </div>
        <form action="{{ route('groups.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 13px; font-weight: 700; margin-bottom: 8px; color: var(--secondary-text);">Tên nhóm</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); color: var(--text-color); outline: none;" placeholder="Ví dụ: Hội Sinh Viên K15">
            </div>
            <button type="submit" class="btn-post" style="width: 100%; padding: 15px; border-radius: 15px; font-weight: 700;">Tạo nhóm ngay</button>
        </form>
    </div>
</div>

<script>
    function copyJoinCode(code) {
        navigator.clipboard.writeText(code);
        alert('Đã sao chép mã nhóm: ' + code);
    }
    function openCreateGroupModal() { document.getElementById('createGroupModal').style.display = 'flex'; document.body.classList.add('modal-open'); }
    function closeCreateGroupModal() { document.getElementById('createGroupModal').style.display = 'none'; document.body.classList.remove('modal-open'); }
    function openGroupPostModal() {
        openModal();
        const form = document.querySelector('#createPostModal form');
        if (form) {
            let groupInput = form.querySelector('input[name="group_id"]');
            if (!groupInput) {
                groupInput = document.createElement('input');
                groupInput.type = 'hidden';
                groupInput.name = 'group_id';
                form.appendChild(groupInput);
            }
            groupInput.value = '{{ $group->id }}';
            const placeholder = form.querySelector('textarea');
            if (placeholder) placeholder.placeholder = "Chia sẻ với nhóm {{ $group->name }}...";
        }
    }
</script>
@endsection
