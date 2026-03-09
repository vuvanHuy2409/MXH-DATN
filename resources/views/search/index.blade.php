@extends('layouts.app')

@section('content')
<div style="padding: 20px 0;">
    <!-- Header & Search Form -->
    <div style="padding: 0 20px;">
        <h2 style="margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -0.5px;">Tìm kiếm</h2>
        
        <form action="{{ route('search') }}" method="GET" id="searchForm" style="margin-top: 20px; position: relative;">
            <div style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--secondary-text); opacity: 0.6;">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </div>
            <input type="text" name="q" value="{{ $query }}" placeholder="Tìm kiếm người dùng hoặc bài viết..." 
                   style="width: 100%; padding: 14px 15px 14px 45px; border-radius: 16px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.03); color: var(--text-color); font-size: 16px; outline: none; box-sizing: border-box; transition: all 0.3s;"
                   onfocus="this.style.background='rgba(0,0,0,0.06)'; this.style.borderColor='var(--accent-color)';"
                   onblur="this.style.background='rgba(0,0,0,0.03)'; this.style.borderColor='var(--glass-border)';"
                   autocomplete="off">
            <input type="hidden" name="type" id="searchType" value="{{ $type }}">
        </form>
    </div>

    @if($query)
        <!-- Tabs Filter -->
        <div style="display: flex; border-bottom: 0.5px solid rgba(0,0,0,0.1); margin-top: 20px;">
            <div onclick="setSearchType('all')" style="flex: 1; text-align: center; padding: 15px; cursor: pointer; font-weight: 700; font-size: 15px; color: {{ $type === 'all' ? 'var(--text-color)' : 'var(--secondary-text)' }}; border-bottom: {{ $type === 'all' ? '2px solid var(--text-color)' : 'none' }}; transition: all 0.2s;">
                Tất cả
            </div>
            <div onclick="setSearchType('people')" style="flex: 1; text-align: center; padding: 15px; cursor: pointer; font-weight: 700; font-size: 15px; color: {{ $type === 'people' ? 'var(--text-color)' : 'var(--secondary-text)' }}; border-bottom: {{ $type === 'people' ? '2px solid var(--text-color)' : 'none' }}; transition: all 0.2s;">
                Người dùng
            </div>
            <div onclick="setSearchType('posts')" style="flex: 1; text-align: center; padding: 15px; cursor: pointer; font-weight: 700; font-size: 15px; color: {{ $type === 'posts' ? 'var(--text-color)' : 'var(--secondary-text)' }}; border-bottom: {{ $type === 'posts' ? '2px solid var(--text-color)' : 'none' }}; transition: all 0.2s;">
                Bài viết
            </div>
        </div>
    @endif

    <div style="margin-top: 10px;">
        @if(!$query)
            <!-- Suggestions when no query -->
            <div style="padding: 10px 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: var(--secondary-text); margin-bottom: 15px;">Gợi ý cho bạn</h3>
                @foreach($suggestions as $suggestUser)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0;">
                        <div style="display: flex; align-items: center; gap: 15px; flex-grow: 1;">
                            <a href="{{ route('profile.show', $suggestUser->username) }}" style="text-decoration: none;">
                                <div class="avatar" style="background-image: url('{{ $suggestUser->avatar_url }}'); background-size: cover; width: 48px; height: 48px;"></div>
                            </a>
                            <div style="overflow: hidden;">
                                <a href="{{ route('profile.show', $suggestUser->username) }}" style="text-decoration: none; color: var(--text-color);">
                                    <div style="font-weight: 700; font-size: 15px;">{{ $suggestUser->username }}</div>
                                </a>
                                <div style="color: var(--secondary-text); font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $suggestUser->followers_count ?? 0 }} người theo dõi
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('users.follow', $suggestUser) }}" method="POST">
                            @csrf
                            <button type="submit" style="background: transparent; border: 1px solid var(--glass-border); color: var(--text-color); padding: 8px 18px; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.05)'" onmouseout="this.style.background='transparent'">
                                Theo dõi
                            </button>
                        </form>
                    </div>
                    <div class="post-divider" style="opacity: 0.3; margin-left: 63px;"></div>
                @endforeach
            </div>
        @else
            <!-- Search Results Content -->
            
            <!-- Users Results -->
            @if(($type === 'all' || $type === 'people') && $users->isNotEmpty())
                <div style="padding: 10px 20px;">
                    @if($type === 'all') <h3 style="font-size: 16px; font-weight: 700; color: var(--secondary-text); margin-bottom: 15px;">Người dùng</h3> @endif
                    
                    @foreach($users as $u)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0;">
                            <div style="display: flex; align-items: center; gap: 15px; flex-grow: 1;">
                                <a href="{{ route('profile.show', $u->username) }}" style="text-decoration: none;">
                                    <div class="avatar" style="background-image: url('{{ $u->avatar_url }}'); background-size: cover; width: 48px; height: 48px;"></div>
                                </a>
                                <div>
                                    <a href="{{ route('profile.show', $u->username) }}" style="text-decoration: none; color: var(--text-color);">
                                        <div style="font-weight: 700; font-size: 15px;">{{ $u->username }}</div>
                                    </a>
                                    <div style="color: var(--secondary-text); font-size: 14px;">{{ $u->followers_count ?? 0 }} người theo dõi</div>
                                </div>
                            </div>
                            
                            @if(auth()->id() !== $u->id)
                                @php $isFollowing = auth()->user()->following->contains($u->id); @endphp
                                <form action="{{ route('users.follow', $u) }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: {{ $isFollowing ? 'transparent' : 'var(--text-color)' }}; border: 1px solid {{ $isFollowing ? 'var(--glass-border)' : 'var(--text-color)' }}; color: {{ $isFollowing ? 'var(--text-color)' : 'white' }}; padding: 8px 18px; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s;">
                                        {{ $isFollowing ? 'Đang theo dõi' : 'Theo dõi' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="post-divider" style="opacity: 0.3; margin-left: 63px;"></div>
                    @endforeach
                </div>
            @endif

            <!-- Posts Results -->
            @if(($type === 'all' || $type === 'posts') && $posts->isNotEmpty())
                <div style="margin-top: 10px;">
                    @if($type === 'all') 
                        <div style="padding: 10px 20px;">
                            <h3 style="font-size: 16px; font-weight: 700; color: var(--secondary-text); margin: 0;">Bài viết</h3> 
                        </div>
                    @endif
                    
                    @foreach($posts as $post)
                        <div class="post-card" style="padding: 20px; border-bottom: none;">
                            <a href="{{ route('profile.show', $post->user->username) }}" style="flex-shrink: 0;">
                                <div class="avatar" style="background-image: url('{{ $post->user->avatar_url }}'); background-size: cover; width: 45px; height: 45px;"></div>
                            </a>
                            <div class="post-content" style="flex-grow: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <a href="{{ route('profile.show', $post->user->username) }}" style="text-decoration: none; color: var(--text-color); font-weight: 700; font-size: 15px;">
                                            {{ $post->user->username }}
                                        </a>
                                        <span style="color: var(--secondary-text); font-size: 13px;">{{ $post->created_at->diffForHumans() }}</span>
                                        @if(auth()->id() !== $post->user_id && !auth()->user()->following->contains($post->user_id))
                                            <form action="{{ route('users.follow', $post->user) }}" method="POST" style="margin: 0; display: inline;">
                                                @csrf
                                                <button type="submit" style="background: var(--text-color); color: white; border: none; padding: 4px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; margin-left: 5px;">Theo dõi</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                
                                <a href="{{ route('posts.show', $post->id) }}" style="text-decoration: none; color: inherit;">
                                    <div class="post-text" style="margin-top: 5px; font-size: 15px; line-height: 1.5; color: var(--text-color);">{{ $post->content }}</div>
                                </a>

                                @if($post->media->isNotEmpty())
                                    <div class="post-media-container" style="margin-top: 12px;">
                                        @foreach($post->media as $media)
                                            <div class="post-media-item {{ $post->media->count() === 1 ? 'single' : '' }}">
                                                @if($media->media_type === 'video')
                                                    <video src="{{ asset($media->media_url) }}" controls></video>
                                                @else
                                                    <img src="{{ asset($media->media_url) }}" onclick="openLightbox(this.src)" style="cursor: zoom-in;">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="post-actions" style="margin-top: 15px; display: flex; gap: 20px; color: var(--secondary-text); opacity: 0.8;">
                                    <div style="display: flex; align-items: center; gap: 5px; font-size: 13px;">
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                        {{ $post->likes_count }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="post-divider"></div>
                    @endforeach
                </div>
            @endif

            @if($users->isEmpty() && $posts->isEmpty())
                <div style="padding: 100px 20px; text-align: center; color: var(--secondary-text);">
                    <div style="margin-bottom: 15px; opacity: 0.2;">
                        <svg viewBox="0 0 24 24" width="64" height="64" stroke="currentColor" stroke-width="1.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    Không tìm thấy kết quả nào cho "{{ $query }}"
                </div>
            @endif
        @endif
    </div>
</div>

<script>
    function setSearchType(type) {
        document.getElementById('searchType').value = type;
        document.getElementById('searchForm').submit();
    }
</script>
@endsection
