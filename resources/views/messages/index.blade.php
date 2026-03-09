@extends('layouts.app')

@section('content')
<style>
    /* Mở rộng container cho trang tin nhắn */
    .container {
        max-width: 1100px !important;
    }

    .chat-layout {
        display: flex;
        height: calc(100vh - 100px);
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        overflow: hidden;
        margin: 20px 0;
        box-shadow: var(--glass-shadow);
    }

    /* Sidebar danh sách hội thoại */
    .chat-sidebar {
        width: 350px;
        border-right: 1px solid var(--glass-border);
        display: flex;
        flex-direction: column;
        background: rgba(255, 255, 255, 0.05);
    }

    .chat-sidebar-header {
        padding: 25px 20px 15px;
    }

    .conversation-list-container {
        flex-grow: 1;
        overflow-y: auto;
    }

    /* Vùng trống bên phải */
    .chat-main-empty {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: var(--secondary-text);
        text-align: center;
        padding: 40px;
    }

    .conversation-item-sidebar {
        padding: 15px 20px;
        display: flex;
        gap: 12px;
        align-items: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
        border-left: 4px solid transparent;
    }

    .conversation-item-sidebar:hover {
        background: rgba(0, 0, 0, 0.03);
    }

    /* Responsive */
    @media (max-width: 900px) {
        .chat-layout {
            border-radius: 0;
            border: none;
            margin: 0;
            height: calc(100vh - 60px);
        }
        .chat-sidebar {
            width: 100%;
            border-right: none;
        }
        .chat-main-empty {
            display: none; /* Trên mobile trang index chỉ hiện list */
        }
        .container {
            max-width: 100% !important;
            padding: 0 !important;
        }
    }
</style>

<div class="chat-layout">
    <!-- Cột trái: Danh sách hội thoại -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0; font-size: 24px; font-weight: 800;">Tin nhắn</h2>
                <div style="display: flex; gap: 8px;">
                    <button onclick="openJoinCodeModal()" style="background: rgba(0,0,0,0.05); border: none; padding: 8px; border-radius: 10px; cursor: pointer;" title="Nhập mã">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    </button>
                    <button onclick="openGroupModal()" style="background: var(--accent-color); border: none; color: white; padding: 8px; border-radius: 10px; cursor: pointer;" title="Nhóm mới">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </button>
                </div>
            </div>
            
            <!-- Search -->
            <div style="position: relative; margin-bottom: 10px;">
                <input type="text" id="dmSearch" placeholder="Tìm kiếm bạn bè..." style="width: 100%; padding: 12px 15px 12px 40px; border-radius: 15px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-size: 14px; outline: none; box-sizing: border-box;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <div id="dmSearchResults" class="glass-bubble" style="display: none; position: absolute; left: 20px; right: 20px; top: 120px; z-index: 1000; max-height: 300px; overflow-y: auto; padding: 10px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);"></div>
        </div>

        <div class="conversation-list-container">
            @forelse($conversations as $conv)
                @php
                    $convIsGroup = $conv->type === 'group';
                    $convName = $conv->name;
                    $convAvatar = $conv->avatar_url;
                    if (!$convIsGroup) {
                        $other = $conv->users->where('id', '!=', auth()->id())->first();
                        $convName = $other ? $other->username : 'Người dùng';
                        $convAvatar = $other ? $other->avatar_url : null;
                    }
                    $convUnread = $conv->lastMessage && !$conv->lastMessage->is_read && $conv->lastMessage->sender_id !== auth()->id();
                @endphp
                <a href="{{ route('messages.show', $conv->id) }}" class="conversation-item-sidebar">
                    <div style="position: relative; flex-shrink: 0;">
                        <div class="avatar" style="background-image: url('{{ $convAvatar ?? asset('default-avatar.png') }}'); background-size: cover; width: 52px; height: 52px; border: {{ $convIsGroup ? '1.5px solid var(--accent-color)' : 'none' }}"></div>
                        @if(!$convIsGroup && $other && $other->is_online)
                            <div style="position: absolute; bottom: 2px; right: 2px; width: 14px; height: 14px; background: #34c759; border: 2.5px solid white; border-radius: 50%;"></div>
                        @endif
                    </div>
                    <div style="flex-grow: 1; overflow: hidden;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <span style="font-weight: 700; font-size: 15px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 70%;">{{ $convName }}</span>
                            <span style="font-size: 11px; opacity: 0.6;">{{ $conv->updated_at->diffForHumans(null, true) }}</span>
                        </div>
                        <div style="font-size: 13px; color: var(--secondary-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 3px; font-weight: {{ $convUnread ? '700' : '400' }}">
                            {{ $conv->lastMessage ? ($conv->lastMessage->sender_id === auth()->id() ? 'Bạn: ' : '') . ($conv->lastMessage->message_type === 'text' ? $conv->lastMessage->content : 'Đã gửi một file') : 'Bắt đầu trò chuyện...' }}
                        </div>
                    </div>
                    @if($convUnread)
                        <div style="width: 10px; height: 10px; background: var(--accent-color); border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 10px rgba(0, 113, 227, 0.4);"></div>
                    @endif
                </a>
            @empty
                <div style="padding: 40px 20px; text-align: center; opacity: 0.5; font-size: 14px;">Chưa có cuộc trò chuyện nào.</div>
            @endforelse
        </div>
    </div>

    <!-- Cột phải: Trạng thái trống -->
    <div class="chat-main-empty">
        <div style="width: 100px; height: 100px; background: rgba(0,113,227,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; color: var(--accent-color);">
            <svg viewBox="0 0 24 24" width="50" height="50" stroke="currentColor" stroke-width="1.5" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
        </div>
        <h3 style="margin: 0; font-size: 20px; font-weight: 800; color: var(--text-color);">Tin nhắn của bạn</h3>
        <p style="margin: 10px 0 25px; font-size: 15px; max-width: 300px; line-height: 1.5;">Chọn một cuộc trò chuyện có sẵn hoặc bắt đầu một cuộc hội thoại mới để gửi tin nhắn.</p>
        <button onclick="openGroupModal()" style="background: var(--accent-color); color: white; border: none; padding: 12px 25px; border-radius: 20px; font-weight: 700; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">Gửi tin nhắn</button>
    </div>
</div>

<!-- Group Modal -->
<div id="groupModal" class="modal">
    <div class="modal-content glass-bubble" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; border-radius: 28px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Tạo nhóm mới</h3>
            <span onclick="closeGroupModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
        </div>
        
        <form action="{{ route('messages.group') }}" method="POST" style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden; padding: 20px;">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 700;">Tên nhóm</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03);" placeholder="Nhập tên nhóm...">
            </div>

            <div style="margin-bottom: 10px;">
                <label style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 700;">Thêm thành viên</label>
                <div id="selectedChips" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;"></div>
                <input type="text" id="friendSearch" placeholder="Tìm tên bạn bè..." style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03);">
            </div>

            <div id="friendsList" style="max-height: 200px; overflow-y: auto; background: rgba(0,0,0,0.02); border-radius: 12px; padding: 5px;"></div>
            <div id="hiddenInputs"></div>

            <button type="submit" class="btn-post" style="width: 100%; padding: 14px; margin-top: 20px; border-radius: 14px;">Tạo nhóm trò chuyện</button>
        </form>
    </div>
</div>

<!-- Join Code Modal -->
<div id="joinCodeModal" class="modal">
    <div class="modal-content glass-bubble" style="max-width: 400px; border-radius: 28px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Gia nhập bằng mã</h3>
            <span onclick="closeJoinCodeModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
        </div>
        <form action="{{ route('messages.join_by_code') }}" method="POST" style="padding: 25px;">
            @csrf
            <div style="margin-bottom: 20px;">
                <input type="text" name="code" maxlength="5" required 
                       style="width: 100%; padding: 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-size: 24px; font-weight: 800; text-align: center; text-transform: uppercase; letter-spacing: 5px;" 
                       placeholder="ABCDE" autofocus>
            </div>
            <button type="submit" class="btn-post" style="width: 100%; padding: 14px; border-radius: 14px;">Tham gia</button>
        </form>
    </div>
</div>

<script>
    const friendsSearchUrl = "{{ url('/api/friends/search') }}";
    const selectedFriends = new Map();

    function openGroupModal() { document.getElementById('groupModal').style.display = 'flex'; loadFriends(''); }
    function closeGroupModal() { document.getElementById('groupModal').style.display = 'none'; }
    function openJoinCodeModal() { document.getElementById('joinCodeModal').style.display = 'flex'; }
    function closeJoinCodeModal() { document.getElementById('joinCodeModal').style.display = 'none'; }

    // Search bạn bè để thêm vào nhóm
    document.getElementById('friendSearch').addEventListener('input', function() {
        loadFriends(this.value.trim());
    });

    function loadFriends(query) {
        fetch(friendsSearchUrl + '?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(friends => {
                const list = document.getElementById('friendsList');
                list.innerHTML = '';
                friends.forEach(f => {
                    const div = document.createElement('div');
                    div.style.cssText = 'display:flex; align-items:center; gap:10px; padding:10px; cursor:pointer; border-radius:10px;';
                    div.onmouseover = () => div.style.background = 'rgba(0,0,0,0.03)';
                    div.onmouseout = () => div.style.background = 'transparent';
                    div.innerHTML = `<div class="avatar" style="width:32px; height:32px; background-image:url('${f.avatar_url}'); background-size:cover;"></div>
                                     <span style="font-size:14px; font-weight:600;">${f.username}</span>`;
                    div.onclick = () => toggleFriend(f);
                    list.appendChild(div);
                });
            });
    }

    function toggleFriend(f) {
        if(selectedFriends.has(f.id)) selectedFriends.delete(f.id);
        else selectedFriends.set(f.id, f);
        renderChips();
    }

    function renderChips() {
        const container = document.getElementById('selectedChips');
        const hidden = document.getElementById('hiddenInputs');
        container.innerHTML = ''; hidden.innerHTML = '';
        selectedFriends.forEach(f => {
            container.innerHTML += `<div style="background:var(--accent-color); color:white; padding:5px 12px; border-radius:15px; font-size:12px; font-weight:700;">${f.username}</div>`;
            hidden.innerHTML += `<input type="hidden" name="user_ids[]" value="${f.id}">`;
        });
    }

    // Search inbox chính
    document.getElementById('dmSearch').addEventListener('input', function() {
        const q = this.value.trim();
        const results = document.getElementById('dmSearchResults');
        if(q.length < 1) { results.style.display = 'none'; return; }
        
        fetch(friendsSearchUrl + '?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(friends => {
                results.innerHTML = '';
                friends.forEach(f => {
                    const a = document.createElement('a');
                    a.href = '/messages/direct/' + f.id;
                    a.style.cssText = 'display:flex; align-items:center; gap:12px; padding:12px; text-decoration:none; color:inherit;';
                    a.innerHTML = `<div class="avatar" style="width:40px; height:40px; background-image:url('${f.avatar_url}'); background-size:cover;"></div>
                                   <div><div style="font-weight:700;">${f.username}</div><div style="font-size:12px; opacity:0.6;">Nhắn tin trực tiếp</div></div>`;
                    results.appendChild(a);
                });
                results.style.display = 'block';
            });
    });

    window.onclick = (e) => {
        if(e.target.classList.contains('modal')) { closeGroupModal(); closeJoinCodeModal(); }
    }
</script>
@endsection
