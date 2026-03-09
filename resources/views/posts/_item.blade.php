@php
    $isOwner = $post->user_id === auth()->id();
    $isFollowing = auth()->user()->following->contains($post->user_id);
    
    // Kiểm tra loại đối tượng (Post hoặc Comment)
    $isCommentType = $post instanceof \App\Models\Comment;
    
    // Likes và Reposts chỉ dành cho Post (theo thiết kế hiện tại của bạn)
    $hasLiked = (!$isCommentType) ? $post->likes->contains('user_id', auth()->id()) : false;
    $hasReposted = (!$isCommentType) ? \App\Models\Repost::where('post_id', $post->id)->where('user_id', auth()->id())->exists() : false;
    
    $uniqueId = ($prefix ?? 'p') . '-' . $post->id;
    
    // Reposters chỉ dành cho Post
    $reposterNames = (!$isCommentType) ? ($post->followed_reposters ?? (isset($repostedBy) ? [$repostedBy] : [])) : [];
    $isRepostView = count($reposterNames) > 0;
@endphp

<div class="post-card {{ $isRepostView ? 'is-repost' : '' }} {{ $class ?? '' }} {{ isset($small) && $small ? 'post-small' : '' }}" id="post-{{ $uniqueId }}" 
     style="{{ $isRepostView ? 'border: 1px solid rgba(0, 195, 0, 0.15); border-radius: 20px; background: rgba(0, 195, 0, 0.02); margin-bottom: 15px;' : '' }}">
    
    <div style="display: flex; gap: 12px; width: 100%;">
        <!-- Cột Avatar -->
        <div style="display: flex; flex-direction: column; align-items: center; width: {{ isset($small) && $small ? '35px' : '45px' }}; flex-shrink: 0;">
            <a href="{{ route('profile.show', $post->user->username) }}">
                <div class="avatar" style="background-image: url('{{ $post->user->avatar_url }}'); background-size: cover; width: {{ isset($small) && $small ? '35px' : '45px' }}; height: {{ isset($small) && $small ? '35px' : '45px' }}; border-radius: 50%;"></div>
            </a>
        </div>

        <!-- Cột Nội dung -->
        <div class="post-content" style="flex-grow: 1; min-width: 0;">
            <!-- Nhãn đăng lại nằm trên tên -->
            @if($isRepostView)
            <div class="repost-label" style="margin-bottom: 4px; color: #00c300; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px; opacity: 0.9;">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                <span>
                    @if(count($reposterNames) == 1)
                        {{ $reposterNames[0] == auth()->user()->username ? 'Bạn' : $reposterNames[0] }} đã đăng lại
                    @elseif(count($reposterNames) <= 3)
                        {{ implode(', ', $reposterNames) }} đã đăng lại
                    @else
                        {{ $reposterNames[0] }}, {{ $reposterNames[1] }} và {{ count($reposterNames) - 2 }} người khác đã đăng lại
                    @endif
                </span>
            </div>
            @endif

            <div class="post-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <a href="{{ route('profile.show', $post->user->username) }}" style="text-decoration: none; color: var(--text-color); font-weight: 700; font-size: 15px;">{{ $post->user->username }}</a>
                    @if($post->user->user_type === 'teacher')
                        <span style="background: var(--accent-color); color: white; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Giáo viên</span>
                    @endif
                    <span style="color: var(--secondary-text); font-size: 13px;" title="{{ $post->created_at->format('H:i d/m/Y') }}">
                        @if($post->created_at->diffInSeconds(now()) < 60)
                            vừa xong
                        @else
                            {{ $post->created_at->diffForHumans() }}
                        @endif
                    </span>
                    
                    <!-- Nút 3 chấm nằm cạnh thời gian -->
                    <div class="dropdown">
                        <div onclick="toggleDropdown('{{ $uniqueId }}')" style="cursor: pointer; color: var(--secondary-text); padding: 5px; opacity: 0.6; display: flex; align-items: center;">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                        </div>
                        <div id="dropdown-{{ $uniqueId }}" class="dropdown-content">
                            @if($isCommentType)
                                <button onclick="alert('Đã báo cáo!')" class="danger"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg> Báo cáo</button>
                                @if($isOwner)
                                <button class="danger" onclick="deleteComment({{ $post->id }})"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> Xóa</button>
                                @endif
                            @else
                                <button onclick="alert('Đã lưu!')"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg> Lưu</button>
                                <button onclick="sharePost({{ $post->id }})"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path></svg> Sao chép link</button>
                                @if($isOwner)
                                <button class="danger" onclick="deletePost({{ $post->id }})"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> Xóa</button>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if(!$isOwner && !$isFollowing)
                        <form action="{{ route('users.follow', $post->user) }}" method="POST" style="margin: 0; display: inline;">
                            @csrf
                            <button type="submit" style="background: var(--text-color); color: white; border: none; padding: 4px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer; margin-left: 5px;">Theo dõi</button>
                        </form>
                    @endif
                </div>
            </div>
            
            @if(isset($isComment) && $isComment)
                <div class="post-text" style="margin-top: 5px; font-size: 14px; line-height: 1.5;">{{ $post->content }}</div>
            @else
                <a href="{{ route('posts.show', $post->id) }}" style="text-decoration: none; color: inherit;">
                    <div class="post-text" style="margin-top: 5px; font-size: 15px; line-height: 1.5;">{{ $post->content }}</div>
                </a>
            @endif

            @if(!$isCommentType && $post->media && $post->media->isNotEmpty())
            <div class="post-media-container" style="margin-top: 12px;">
                @if($post->media->count() === 1)
                <div class="post-media-single">
                    @php $media = $post->media->first(); @endphp
                    @if($media->media_type === 'video')
                    <video src="{{ asset($media->media_url) }}" controls></video>
                    @else
                    <img src="{{ asset($media->media_url) }}" onclick="openLightbox(this.src)" style="cursor: zoom-in;">
                    @endif
                </div>
                @else
                <div class="post-media-horizontal">
                    @foreach($post->media as $media)
                    <div class="post-media-item">
                        @if($media->media_type === 'video')
                        <video src="{{ asset($media->media_url) }}" controls></video>
                        @else
                        <img src="{{ asset($media->media_url) }}" onclick="openLightbox(this.src)" style="cursor: zoom-in;">
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            <div class="post-actions" style="margin-top: 18px; display: flex; gap: 25px; color: var(--secondary-text);">
                <!-- Like -->
                @if(!isset($hideLike))
                <div class="action-btn like-btn {{ $hasLiked ? 'liked' : '' }}" onclick="toggleLike({{ $post->id }})" data-post-id="{{ $post->id }}" style="cursor: pointer; display: flex; align-items: center; gap: 6px; {{ $hasLiked ? 'color: #ff3b30;' : '' }}">
                    <span class="like-icon" id="like-icon-{{ $post->id }}"><svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="{{ $hasLiked ? 'currentColor' : 'none' }}" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></span>
                    <span class="like-count" id="like-count-{{ $post->id }}" style="font-size: 13px; font-weight: 600;">{{ $post->like_count }}</span>
                </div>
                @endif

                <!-- Comment / Reply -->
                <div class="action-btn comment-btn" 
                     @if(request()->routeIs('posts.show'))
                        onclick="prepareQuickReply({{ $post->id }}, '{{ addslashes($post->user->username) }}')"
                     @else
                        onclick="window.location.href='{{ route('posts.show', $post->id) }}'"
                     @endif
                     style="cursor: pointer; display: flex; align-items: center; gap: 6px;">
                    @if(isset($isComment) && $isComment)
                        <span style="font-size: 13px; font-weight: 700;">Trả lời</span>
                    @else
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        <span class="comment-count-display" data-post-id="{{ $post->id }}" style="font-size: 13px; font-weight: 600;">{{ $post->reply_count }}</span>
                    @endif
                </div>

                <!-- Repost -->
                @if(!isset($hideRepost) && !$isCommentType)
                <div class="action-btn repost-btn {{ $hasReposted ? 'reposted' : '' }}" onclick="toggleRepost({{ $post->id }})" data-post-id="{{ $post->id }}" style="cursor: pointer; display: flex; align-items: center; gap: 6px; {{ $hasReposted ? 'color: #00c300;' : '' }}">
                    <span id="repost-icon-{{ $post->id }}" style="display: flex; align-items: center;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                    </span>
                    <span class="repost-count" id="repost-count-{{ $post->id }}" style="font-size: 13px; font-weight: 500;">{{ $post->reposts_count ?? 0 }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@if(!$isRepostView) <div class="post-divider"></div> @endif
