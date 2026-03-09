@extends('layouts.app')

@section('content')
<div style="display: flex; flex-direction: column; min-height: calc(100vh - 120px);">
    <!-- Header -->
    <div style="padding: 25px 20px 10px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">Tin nhắn</h2>
        <div style="display: flex; gap: 10px;">
            <button onclick="openJoinCodeModal()" style="background: rgba(0,0,0,0.05); border: 1px solid var(--glass-border); color: var(--text-color); padding: 8px 16px; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.2s;">
                Nhập mã
            </button>
            <button onclick="openGroupModal()" style="background: rgba(255,255,255,0.2); border: 1px solid var(--glass-border); color: var(--text-color); padding: 8px 16px; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 600; backdrop-filter: blur(10px); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Nhóm mới
                </div>
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div style="padding: 10px 20px 10px; position: relative;">
        <div style="position: relative; display: flex; align-items: center;">
            <div style="position: absolute; left: 15px; color: var(--secondary-text); opacity: 0.6;">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <input type="text" id="dmSearch" placeholder="Tìm kiếm bạn bè..." autocomplete="off"
                style="width: 100%; border: 1px solid var(--glass-border); border-radius: 12px; padding: 12px 15px 12px 45px; background: rgba(0,0,0,0.03); color: var(--text-color); font-size: 15px; outline: none; box-sizing: border-box; transition: all 0.3s;">
        </div>
        <div id="dmSearchResults" class="glass-bubble" style="display: none; position: absolute; left: 20px; right: 20px; top: 60px; z-index: 1000; max-height: 400px; overflow-y: auto; padding: 10px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);"></div>
    </div>

    <!-- Filters Tabs -->
    <div style="display: flex; padding: 0 20px 15px; gap: 10px; margin-top: 5px; overflow-x: auto; white-space: nowrap;">
        <div onclick="switchFilter('all')" class="filter-tab {{ $filter === 'all' ? 'active' : '' }}">Tất cả</div>
        <div onclick="switchFilter('unread')" class="filter-tab {{ $filter === 'unread' ? 'active' : '' }}">Chưa đọc</div>
        <div onclick="switchFilter('friends')" class="filter-tab {{ $filter === 'friends' ? 'active' : '' }}">Bạn bè</div>
        <div onclick="switchFilter('groups')" class="filter-tab {{ $filter === 'groups' ? 'active' : '' }}">Nhóm</div>
    </div>

    <!-- Conversation List -->
    <div class="conversation-list" style="flex-grow: 1;">
        <div class="post-divider"></div>
        @forelse($conversations as $conversation)
            @php
                $isGroup = $conversation->type === 'group';
                $displayName = $conversation->name;
                $displayAvatar = $conversation->avatar_url;
                if (!$isGroup) {
                    $otherUser = $conversation->users->where('id', '!=', auth()->id())->first();
                    $displayName = $otherUser ? $otherUser->username : 'Người dùng';
                    $displayAvatar = $otherUser ? $otherUser->avatar_url : null;
                }
                $isUnread = $conversation->lastMessage && !$conversation->lastMessage->is_read && $conversation->lastMessage->sender_id !== auth()->id();
            @endphp
            
            <a href="{{ route('messages.show', $conversation->id) }}" class="conversation-item {{ $isGroup ? 'group-style' : '' }}" style="text-decoration: none; color: inherit; display: block; padding: 18px 20px; transition: all 0.2s ease;">
                <div style="display: flex; gap: 14px; align-items: center;">
                    <div style="position: relative; flex-shrink: 0;">
                        @if($isGroup)
                            @if($displayAvatar)
                                <div class="avatar" style="background-image: url('{{ $displayAvatar }}'); background-size: cover; width: 52px; height: 52px; border: 1.5px solid var(--accent-color);"></div>
                            @else
                                <!-- Group Avatar Layout Default -->
                                <div style="width: 52px; height: 52px; position: relative; background: rgba(0,113,227,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--accent-color);">
                                    <svg viewBox="0 0 24 24" width="26" height="26" stroke="var(--accent-color)" stroke-width="2" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                </div>
                            @endif
                        @else
                            <div class="avatar" style="background-image: url('{{ $displayAvatar ?? asset('default-avatar.png') }}'); background-size: cover; width: 52px; height: 52px;"></div>
                            @if($otherUser && $otherUser->is_online)
                                <div style="position: absolute; bottom: 2px; right: 2px; width: 14px; height: 14px; background: #34c759; border: 2.5px solid white; border-radius: 50%;"></div>
                            @endif
                        @endif
                    </div>
                    
                    <div style="flex-grow: 1; overflow: hidden;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 3px;">
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="font-weight: 700; font-size: 16px; color: var(--text-color); letter-spacing: -0.2px;">{{ $displayName }}</span>
                                @if($isGroup)
                                    <span style="font-size: 10px; background: var(--accent-color); color: white; padding: 2px 8px; border-radius: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Nhóm</span>
                                @endif
                            </div>
                            <span style="font-size: 12px; color: var(--secondary-text); font-weight: 500; opacity: 0.8;">{{ $conversation->updated_at->diffForHumans(null, true) }}</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 14px; color: {{ $isUnread ? 'var(--text-color)' : 'var(--secondary-text)' }}; font-weight: {{ $isUnread ? '700' : '400' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 85%;">
                                @if($conversation->lastMessage)
                                    @if($conversation->lastMessage->message_type === 'system')
                                        <span style="font-style: italic; opacity: 0.8; color: var(--accent-color);">📢 {{ $conversation->lastMessage->content }}</span>
                                    @else
                                        @if($conversation->lastMessage->sender_id === auth()->id())
                                            <span style="opacity: 0.6;">Bạn: </span>
                                        @elseif($isGroup)
                                            <span style="opacity: 0.6;">{{ $conversation->lastMessage->sender->username ?? 'Người dùng' }}: </span>
                                        @endif
                                        
                                        @if($conversation->lastMessage->message_type === 'image')
                                            🖼️ Hình ảnh
                                        @elseif($conversation->lastMessage->message_type === 'video')
                                            🎥 Video
                                        @elseif($conversation->lastMessage->message_type === 'file')
                                            📁 Tệp tin
                                        @else
                                            {{ $conversation->lastMessage->content }}
                                        @endif
                                    @endif
                                @else
                                    <span style="font-style: italic; opacity: 0.5;">Bắt đầu trò chuyện...</span>
                                @endif
                            </div>
                            
                            @if($isUnread)
                                <div style="width: 8px; height: 8px; background: var(--accent-color); border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 10px rgba(0, 113, 227, 0.4);"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
            <div class="post-divider"></div>
        @empty
            <div style="padding: 120px 20px; text-align: center; color: var(--secondary-text);">
                <div style="margin-bottom: 20px; opacity: 0.15;">
                    <svg viewBox="0 0 24 24" width="80" height="80" stroke="currentColor" stroke-width="1" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                </div>
                <div style="font-size: 16px; font-weight: 600;">Hộp thư trống</div>
                <div style="font-size: 14px; margin-top: 8px; opacity: 0.6;">Những tin nhắn mới sẽ xuất hiện tại đây.</div>
            </div>
        @endforelse
    </div>
</div>

<!-- Group Modal -->
<div id="groupModal" class="modal">
    <div class="modal-content glass-bubble" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; border-radius: 28px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Tạo nhóm mới</h3>
            <span class="close-modal" onclick="closeGroupModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5; padding: 5px;">&times;</span>
        </div>
        
        <form id="groupForm" action="{{ route('messages.group') }}" method="POST" style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden;">
            @csrf
            <div style="padding: 20px; overflow-y: auto; flex-grow: 1;">
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 10px; font-size: 14px; font-weight: 700; opacity: 0.9;">Tên cuộc trò chuyện</label>
                    <input type="text" name="name" required style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03);" placeholder="Ví dụ: Nhóm học tập, Gia đình...">
                </div>

                <div id="selectedChips" style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 15px;"></div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 10px; font-size: 14px; font-weight: 700; opacity: 0.9;">Thêm thành viên</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <div style="position: absolute; left: 12px; opacity: 0.4;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </div>
                        <input type="text" id="friendSearch" placeholder="Tìm kiếm bạn bè..." style="width: 100%; padding: 12px 12px 12px 40px; box-sizing: border-box; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03);" autocomplete="off">
                    </div>
                </div>

                <div id="friendsList" style="max-height: 280px; overflow-y: auto; background: rgba(0,0,0,0.02); border: 1px solid var(--glass-border); border-radius: 16px;">
                    <div style="padding: 40px 20px; text-align: center; color: var(--secondary-text); font-size: 13px; opacity: 0.6;">
                        Tìm kiếm người dùng bằng tên hoặc email để thêm vào nhóm.
                    </div>
                </div>
                <div id="hiddenInputs"></div>
            </div>

            <div style="padding: 20px; border-top: 1px solid var(--glass-border); background: rgba(0,0,0,0.02);">
                <button type="submit" class="btn-post" style="width: 100%; padding: 14px; border-radius: 14px; font-size: 15px;">Bắt đầu trò chuyện nhóm</button>
            </div>
        </form>
    </div>
</div>

<style>
    .filter-tab {
        padding: 8px 16px;
        border-radius: 20px;
        background: rgba(0,0,0,0.04);
        font-size: 14px;
        font-weight: 700;
        color: var(--secondary-text);
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    
    .filter-tab:hover {
        background: rgba(0,0,0,0.08);
    }
    
    .filter-tab.active {
        background: var(--text-color);
        color: white;
    }

    .conversation-item:hover {
        background: rgba(0, 0, 0, 0.03) !important;
    }
    
    .conversation-item.group-style {
        background: rgba(0, 113, 227, 0.02);
    }
    
    .conversation-item.group-style:hover {
        background: rgba(0, 113, 227, 0.05) !important;
    }

    [data-theme="dark"] .filter-tab {
        background: rgba(255,255,255,0.08);
    }
    
    [data-theme="dark"] .conversation-item:hover {
        background: rgba(255, 255, 255, 0.05) !important;
    }
    
    [data-theme="dark"] .conversation-item.group-style {
        background: rgba(0, 113, 227, 0.05);
    }
</style>

<!-- Join Code Modal -->
<div id="joinCodeModal" class="modal">
    <div class="modal-content glass-bubble" style="max-width: 400px; padding: 0; overflow: hidden; border-radius: 28px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px; font-weight: 700;">Gia nhập bằng mã</h3>
            <span onclick="closeJoinCodeModal()" style="cursor: pointer; font-size: 24px; opacity: 0.5;">&times;</span>
        </div>
        <form action="{{ route('messages.join_by_code') }}" method="POST" style="padding: 25px;">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; font-size: 14px; font-weight: 700; opacity: 0.8;">Nhập mã nhóm (5 ký tự)</label>
                <input type="text" name="code" maxlength="5" required 
                       style="width: 100%; box-sizing: border-box; padding: 15px; border-radius: 12px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); font-size: 24px; font-weight: 800; text-align: center; text-transform: uppercase; letter-spacing: 5px;" 
                       placeholder="ABCDE" autofocus>
            </div>
            <button type="submit" class="btn-post" style="width: 100%; padding: 14px; border: none; font-weight: 700;">Tham gia nhóm</button>
        </form>
    </div>
</div>

<script>
    function switchFilter(filter) {
        window.location.href = "{{ route('messages.index') }}?filter=" + filter;
    }

    // Friend Search & Selection Logic (Group Chat)
    const selectedFriends = new Map();
    let searchTimeout = null;
    const friendsSearchUrl = "{{ url('/api/friends/search') }}";
    let dmSearchTimeout = null;
    const dmSearchInput = document.getElementById('dmSearch');
    const dmSearchResults = document.getElementById('dmSearchResults');

    dmSearchInput.addEventListener('input', function() {
        clearTimeout(dmSearchTimeout);
        const query = this.value.trim();
        if (query.length === 0) {
            dmSearchResults.style.display = 'none';
            return;
        }
        dmSearchTimeout = setTimeout(() => {
            fetch(friendsSearchUrl + '?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(friends => {
                    if (friends.length === 0) {
                        dmSearchResults.innerHTML = '<div style="padding: 30px 20px; text-align: center; color: var(--secondary-text); font-size: 14px; opacity: 0.6;">Không tìm thấy bạn bè nào</div>';
                        dmSearchResults.style.display = 'block';
                        return;
                    }
                    dmSearchResults.innerHTML = '';
                    friends.forEach(friend => {
                        const item = document.createElement('a');
                        item.href = '/messages/direct/' + friend.id;
                        item.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 12px; cursor: pointer; transition: all 0.2s; text-decoration: none; color: inherit; border-radius: 14px; margin-bottom: 4px;';
                        item.onmouseenter = () => item.style.background = 'rgba(0,0,0,0.05)';
                        item.onmouseleave = () => item.style.background = 'transparent';
                        item.innerHTML =
                            '<div class="avatar" style="width: 44px; height: 44px; background-image: url(\'' + (friend.avatar_url || '') + '\'); background-size: cover; flex-shrink: 0;"></div>' +
                            '<div style="flex-grow: 1;">' +
                            '<div style="font-weight: 700; font-size: 15px;">' + friend.username + '</div>' +
                            '<div style="font-size: 12px; color: var(--secondary-text);">Nhắn tin trực tiếp</div>' +
                            '</div>';
                        dmSearchResults.appendChild(item);
                    });
                    dmSearchResults.style.display = 'block';
                });
        }, 300);
    });

    function openGroupModal() {
        document.getElementById('groupModal').style.display = 'flex';
        document.body.classList.add('modal-open');
        loadFriends('');
    }

    function closeGroupModal() {
        document.getElementById('groupModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function openJoinCodeModal() { 
        document.getElementById('joinCodeModal').style.display = 'flex'; 
        document.body.classList.add('modal-open'); 
    }
    function closeJoinCodeModal() { 
        document.getElementById('joinCodeModal').style.display = 'none'; 
        document.body.classList.remove('modal-open'); 
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('groupModal')) closeGroupModal();
        if (event.target == document.getElementById('joinCodeModal')) closeJoinCodeModal();
    }

    function loadFriends(query) {
        fetch(friendsSearchUrl + '?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(friends => {
                const list = document.getElementById('friendsList');
                if (friends.length === 0) {
                    list.innerHTML = `<div style="padding: 40px 20px; text-align: center; color: var(--secondary-text); font-size: 13px; opacity: 0.6;">
                        ${query ? 'Không tìm thấy người dùng nào' : 'Bắt đầu tìm kiếm để thêm thành viên'}
                    </div>`;
                    return;
                }
                list.innerHTML = '';
                friends.forEach(friend => renderFriendItem(list, friend));
            });
    }

    function renderFriendItem(list, friend) {
        const isSelected = selectedFriends.has(friend.id);
        const item = document.createElement('div');
        item.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 12px; cursor: pointer; transition: all 0.2s;';
        item.onmouseenter = () => item.style.background = 'rgba(0,0,0,0.05)';
        item.onmouseleave = () => item.style.background = 'transparent';
        item.innerHTML =
            '<div class="avatar" style="width: 40px; height: 40px; background-image: url(\'' + (friend.avatar_url || '') + '\'); background-size: cover;"></div>' +
            '<div style="flex-grow: 1; font-weight: 600; font-size: 14px;">' + friend.username + '</div>' +
            '<div style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid ' + (isSelected ? 'var(--accent-color)' : 'rgba(0,0,0,0.2)') + '; background: ' + (isSelected ? 'var(--accent-color)' : 'transparent') + '; flex-shrink: 0;"></div>';
        item.onclick = () => toggleFriend(friend);
        list.appendChild(item);
    }

    document.getElementById('friendSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadFriends(this.value.trim()), 300);
    });

    function toggleFriend(friend) {
        if (selectedFriends.has(friend.id)) selectedFriends.delete(friend.id);
        else selectedFriends.set(friend.id, friend);
        renderChips();
        renderHiddenInputs();
        loadFriends(document.getElementById('friendSearch').value.trim());
    }

    function renderChips() {
        const container = document.getElementById('selectedChips');
        container.innerHTML = '';
        selectedFriends.forEach((friend, id) => {
            const chip = document.createElement('div');
            chip.style.cssText = 'display: flex; align-items: center; gap: 6px; padding: 6px 12px; background: rgba(0, 113, 227, 0.1); border-radius: 20px; font-size: 13px; font-weight: 600; color: var(--accent-color);';
            chip.innerHTML = friend.username + '<span onclick="removeFriend(' + id + ')" style="cursor: pointer; opacity: 0.7; font-size: 16px;">&times;</span>';
            container.appendChild(chip);
        });
    }

    function removeFriend(id) {
        selectedFriends.delete(id);
        renderChips();
        renderHiddenInputs();
        loadFriends(document.getElementById('friendSearch').value.trim());
    }

    function renderHiddenInputs() {
        const container = document.getElementById('hiddenInputs');
        container.innerHTML = '';
        selectedFriends.forEach((_, id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'user_ids[]';
            input.value = id;
            container.appendChild(input);
        });
    }
</script>
@endsection
