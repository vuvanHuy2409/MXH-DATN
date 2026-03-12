@extends('layouts.app')

@section('content')
<div style="padding: 20px;">
    <h2 style="margin-top: 0;">Hoạt động</h2>

    <!-- Tabs lọc thông báo -->
    <div style="display: flex; gap: 12px; margin-bottom: 20px;">
        <div class="notif-tab active-tab glass-bubble" onclick="filterNotifications('all')" id="tab-all" style="flex: 1; text-align: center; padding: 10px; font-weight: 600; cursor: pointer; margin-bottom: 0; border-radius: 20px; font-size: 14px;">
            Tất cả
        </div>
        <div class="notif-tab glass-bubble" onclick="filterNotifications('like')" id="tab-like" style="flex: 1; text-align: center; padding: 10px; color: var(--secondary-text); font-weight: 600; cursor: pointer; margin-bottom: 0; border-radius: 20px; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 14px; background: transparent; border-color: transparent; box-shadow: none;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg> Thích
        </div>
        <div class="notif-tab glass-bubble" onclick="filterNotifications('reply')" id="tab-reply" style="flex: 1; text-align: center; padding: 10px; color: var(--secondary-text); font-weight: 600; cursor: pointer; margin-bottom: 0; border-radius: 20px; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 14px; background: transparent; border-color: transparent; box-shadow: none;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg> Bình luận
        </div>
        <div class="notif-tab glass-bubble" onclick="filterNotifications('follow')" id="tab-follow" style="flex: 1; text-align: center; padding: 10px; color: var(--secondary-text); font-weight: 600; cursor: pointer; margin-bottom: 0; border-radius: 20px; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 14px; background: transparent; border-color: transparent; box-shadow: none;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Theo dõi
        </div>
    </div>

    <!-- Danh sách thông báo -->
    @forelse($notifications as $n)
    @php
        $targetUrl = '#';
        if ($n->type === 'follow') {
            $targetUrl = route('profile.show', $n->actor->username);
        } elseif ($n->post_id) {
            $targetUrl = route('posts.show', $n->post_id);
        }

        $baseBg = 'transparent';
        if (!$n->is_read) {
            $baseBg = 'rgba(0,113,227,0.08)';
        } elseif ($n->type === 'follow') {
            $baseBg = 'rgba(142, 68, 173, 0.03)';
        }
        
        $hoverBg = $n->type === 'follow' ? 'rgba(142, 68, 173, 0.08)' : 'rgba(0,0,0,0.02)';
    @endphp
    <div class="notif-item glass-bubble" data-type="{{ $n->type }}" onclick="window.location.href='{{ $targetUrl }}'" 
         style="display: flex; gap: 15px; align-items: center; cursor: pointer; transition: background 0.2s; background: {{ $baseBg }}; {{ !$n->is_read ? 'border-left: 4px solid var(--accent-color);' : ($n->type === 'follow' ? 'border-left: 4px solid #8e44ad;' : '') }}" 
         onmouseover="this.style.background='{{ $hoverBg }}'" 
         onmouseout="this.style.background='{{ $baseBg }}'">
        <div style="position: relative;" onclick="event.stopPropagation()">
            <a href="{{ route('profile.show', $n->actor->username) }}">
                <div class="avatar" style="background-image: url('{{ $n->actor->avatar_url }}'); background-size: cover;"></div>
            </a>
            <div style="position: absolute; bottom: -2px; right: -2px; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; border: 2px solid var(--bg-color); color: white;
                        @if($n->type == 'like') background: #ff3b30;
                        @elseif($n->type == 'reply') background: #0095f6;
                        @elseif($n->type == 'repost') background: #00c300;
                        @else background: #8e44ad;
                        @endif">
                @if($n->type == 'like') <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                @elseif($n->type == 'reply') <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                @elseif($n->type == 'repost') <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                @else <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="3" fill="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                @endif
            </div>
        </div>

        <div style="flex-grow: 1;">
            <div style="font-size: 14px;">
                @php
                    $actors = $n->aggregate_actors;
                    $count = $n->aggregate_count;
                    $firstActor = $actors->first();
                @endphp
                
                <a href="{{ route('profile.show', $firstActor->username) }}" style="text-decoration: none; color: var(--text-color);">
                    <strong>{{ $firstActor->username }}</strong>
                </a>

                @if($count > 1)
                    <span style="color: var(--secondary-text);"> và {{ $count - 1 }} người khác</span>
                @endif

                <span style="color: var(--secondary-text);">
                    @if($n->type == 'like') đã thích bài viết của bạn.
                    @elseif($n->type == 'reply') đã bình luận bài viết của bạn.
                    @elseif($n->type == 'repost') đã đăng lại bài viết của bạn.
                    @else đã theo dõi bạn.
                    @endif
                </span>

                @if($n->post && $n->post->group)
                    <span style="display: inline-flex; align-items: center; gap: 4px; background: rgba(0,113,227,0.05); padding: 2px 8px; border-radius: 6px; margin-left: 5px; border: 1px solid rgba(0,113,227,0.1);">
                        <img src="{{ $n->post->group->avatar_url }}" style="width: 14px; height: 14px; border-radius: 3px; object-fit: cover;">
                        <span style="font-size: 11px; font-weight: 700; color: var(--accent-color);">{{ $n->post->group->name }}</span>
                    </span>
                @endif

                <span style="color: var(--secondary-text); font-size: 12px; margin-left: 5px;">{{ $n->created_at->diffForHumans() }}</span>
            </div>

            @if($n->post)
            <a href="{{ route('posts.show', $n->post->id) }}" style="text-decoration: none;">
                <div style="margin-top: 5px; color: var(--secondary-text); font-size: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; opacity: 0.8;">
                    {{ $n->post->content }}
                </div>
            </a>
            @endif
        </div>

        @if($n->type == 'follow')
        <form action="{{ route('users.follow', $n->actor) }}" method="POST" onclick="event.stopPropagation()">
            @csrf
            <button type="submit" style="border: 1px solid var(--border-color); background: transparent; color: var(--text-color); padding: 6px 18px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 13px; transition: background 0.2s;">
                Theo dõi lại
            </button>
        </form>
        @endif
    </div>
    @empty
    <div id="empty-state" style="padding: 100px 20px; text-align: center; color: var(--secondary-text);">
        <div style="margin-bottom: 10px; opacity: 0.3;">
            <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
        </div>
        Chưa có thông báo nào.
    </div>
    @endforelse

    <div id="empty-filter" style="display: none; padding: 100px 20px; text-align: center; color: var(--secondary-text);">
        Không có thông báo nào trong danh mục này.
    </div>
</div>

<script>
    function filterNotifications(type) {
        const items = document.querySelectorAll('.notif-item');
        const tabs = ['all', 'like', 'reply', 'follow'];
        let visibleCount = 0;

        // Update tab styles
        tabs.forEach(t => {
            const tab = document.getElementById('tab-' + t);
            if (t === type) {
                tab.style.color = 'var(--text-color)';
                tab.style.background = 'var(--glass-bg)';
                tab.style.borderColor = 'var(--glass-border)';
                tab.style.boxShadow = 'var(--glass-shadow)';
            } else {
                tab.style.color = 'var(--secondary-text)';
                tab.style.background = 'transparent';
                tab.style.borderColor = 'transparent';
                tab.style.boxShadow = 'none';
            }
        });

        // Filter items
        items.forEach(item => {
            if (type === 'all' || item.dataset.type === type) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide empty filter message
        const emptyFilter = document.getElementById('empty-filter');
        const emptyState = document.getElementById('empty-state');
        if (visibleCount === 0 && items.length > 0) {
            emptyFilter.style.display = 'block';
        } else {
            emptyFilter.style.display = 'none';
        }
        if (emptyState) {
            emptyState.style.display = (items.length === 0 && type === 'all') ? 'block' : 'none';
        }
    }
</script>
@endsection