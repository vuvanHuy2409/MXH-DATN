@php
    $isOwner = $post->user_id === auth()->id();
    $isFollowing = auth()->user()->following->contains($post->user_id);
    $isCommentType = $post instanceof \App\Models\Comment;
    $hasLiked = (!$isCommentType) ? $post->likes->contains('user_id', auth()->id()) : false;
    $uniqueId = ($prefix ?? 'p') . '-' . $post->id;
@endphp

<div class="post-card-v2 {{ $class ?? '' }}" id="post-{{ $uniqueId }}">
    <div style="display: flex; gap: 15px;">
        <!-- Left Side: Avatar & Thread Line -->
        <div style="display: flex; flex-direction: column; align-items: center; flex-shrink: 0;">
            <a href="{{ route('profile.show', $post->user->username) }}">
                <div class="avatar" style="background-image: url('{{ $post->user->avatar_url }}'); background-size: cover; width: 44px; height: 44px; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.05);"></div>
            </a>
            @if(!$isCommentType && $post->replies_count > 0)
                <div style="width: 2px; flex-grow: 1; background: var(--glass-border); margin: 8px 0; border-radius: 1px; opacity: 0.5;"></div>
            @endif
        </div>

        <!-- Right Side: Content -->
        <div style="flex-grow: 1; min-width: 0; text-align: left; display: flex; flex-direction: column; align-items: flex-start;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; width: 100%;">
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; text-align: left;">
                    <a href="{{ route('profile.show', $post->user->username) }}" style="text-decoration: none; color: var(--text-color); font-weight: 750; font-size: 15px; letter-spacing: -0.2px;">{{ $post->user->username }}</a>
                    @if($post->user->user_type === 'teacher')
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="var(--accent-color)" style="margin-left: -2px;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                    @endif
                    <span style="color: var(--secondary-text); font-size: 13px; opacity: 0.6;">·</span>
                    <span style="color: var(--secondary-text); font-size: 13px; opacity: 0.6;">{{ $post->created_at->diffForHumans(null, true) }}</span>
                </div>
                
                <div class="dropdown">
                    <div onclick="toggleDropdown('{{ $uniqueId }}')" style="cursor: pointer; color: var(--secondary-text); padding: 4px; opacity: 0.5;">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                    </div>
                    <div id="dropdown-{{ $uniqueId }}" class="dropdown-content" style="border-radius: 16px; border: 1px solid var(--glass-border); box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        @if($isOwner)
                            @if(!$isCommentType)
                                <button onclick="openEditPostModal({{ $post->id }}, '{{ addslashes($post->content) }}')">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> 
                                    Chỉnh sửa
                                </button>
                            @endif
                            <button class="danger" onclick="{{ $isCommentType ? "deleteComment($post->id)" : "deletePost($post->id)" }}"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> Xóa</button>
                        @endif

                        @if(!$isOwner)
                            <button class="danger" onclick="alert('Cảm ơn bạn đã báo cáo. Chúng tôi sẽ xem xét {{ $isCommentType ? 'bình luận' : 'bài viết' }} này sớm nhất có thể.')">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                                Báo cáo {{ $isCommentType ? 'bình luận' : 'bài viết' }}
                            </button>
                        @endif
                        
                        @if(!$isCommentType)
                        <button onclick="alert('Tính năng đang phát triển')"><svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg> Lưu bài viết</button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content Body -->
            <div onclick="if(event.target.tagName !== 'A') window.location.href='{{ route('posts.show', $post->id) }}'" style="cursor: pointer; margin-bottom: 12px; width: 100%; text-align: left;">
                <div style="font-size: 15px; line-height: 1.55; color: var(--text-color); font-weight: 450; white-space: pre-wrap; word-break: break-word; text-align: left;">
                    @php
                        $content = e($post->content);
                        // Regex nhận diện URL: http, https, www hoặc domain.com
                        $urlRegex = '/(https?:\/\/[^\s]+|www\.[^\s]+|[a-z0-9.-]+\.[a-z]{2,}(\/[^\s]*)?)/i';
                        $contentWithLinks = preg_replace_callback($urlRegex, function($matches) {
                            $url = $matches[0];
                            $href = $url;
                            if (!preg_match('/^https?:\/\//i', $url)) {
                                $href = 'https://' . $url;
                            }
                            // Thêm style: Màu sắc, Gạch chân, In nghiêng
                            return '<a href="' . $href . '" target="_blank" onclick="event.stopPropagation()" style="color: var(--accent-color); text-decoration: underline; font-style: italic; font-weight: 600; cursor: pointer;">' . $url . '</a>';
                        }, $content);
                    @endphp
                    {!! $contentWithLinks !!}
                </div>
            </div>

            <!-- Media -->
            @if(!$isCommentType && $post->media && $post->media->isNotEmpty())
            <div style="margin-bottom: 15px; width: 100%; display: flex; justify-content: flex-start;">
                @if($post->media->count() === 1)
                    <div style="border-radius: 18px; overflow: hidden; border: 1px solid var(--glass-border); max-height: 450px; max-width: 550px; background: rgba(0,0,0,0.02); display: flex; justify-content: center; align-items: center;">
                        @php $media = $post->media->first(); @endphp
                        @if($media->media_type === 'video')
                            <video src="{{ asset($media->media_url) }}" controls style="width: 100%; max-height: 450px; display: block; object-fit: contain;"></video>
                        @else
                            <img src="{{ asset($media->media_url) }}" onclick="openLightbox(this.src)" style="width: 100%; max-height: 450px; display: block; cursor: zoom-in; object-fit: cover;">
                        @endif
                    </div>
                @else
                    <div class="media-horizontal-scroll" style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 12px; scrollbar-width: none; -ms-overflow-style: none; scroll-snap-type: x mandatory; max-width: 550px; width: 100%;">
                        @foreach($post->media as $media)
                            <div style="flex: 0 0 160px; height: 160px; border-radius: 16px; overflow: hidden; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.02); scroll-snap-align: start;">
                                @if($media->media_type === 'video')
                                    <video src="{{ asset($media->media_url) }}" controls style="width: 100%; height: 100%; object-fit: cover;"></video>
                                @else
                                    <img src="{{ asset($media->media_url) }}" onclick="openLightbox(this.src)" style="width: 100%; height: 100%; object-fit: cover; cursor: zoom-in;">
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif

            <!-- Link Preview -->
            @if(isset($post->link_url) && $post->link_url)
            <a href="{{ $post->link_url }}" target="_blank" style="text-decoration: none; display: block; margin-bottom: 15px; padding: 12px; border-radius: 14px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.02); display: flex; align-items: center; gap: 10px;">
                <div style="background: white; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="var(--accent-color)" stroke-width="2.5" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </div>
                <div style="flex-grow: 1; overflow: hidden; font-size: 13px; font-weight: 600; color: var(--accent-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $post->link_url }}</div>
            </a>
            @endif

            <!-- Actions -->
            <div style="display: flex; gap: 20px; align-items: center;">
                @if(!isset($hideLike))
                <div class="action-btn-v2 like-btn {{ $hasLiked ? 'liked' : '' }}" onclick="toggleLike({{ $post->id }})" data-post-id="{{ $post->id }}" style="position: relative;">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="{{ $hasLiked ? 'currentColor' : 'none' }}"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <span class="like-count" style="font-size: 13px; font-weight: 600;">{{ $post->like_count }}</span>
                </div>
                @endif

                @if(!isset($hideReply))
                <div class="action-btn-v2" onclick="window.location.href='{{ route('posts.show', $post->id) }}'">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    <span style="font-size: 13px; font-weight: 600;">{{ $post->reply_count ?? 0 }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .post-card-v2 {
        padding: 25px;
        transition: all 0.2s ease;
        position: relative;
        background: var(--glass-bg);
        border: 1px solid rgba(0,0,0,0.1); /* Viền nhạt hơn */
        border-radius: 28px;
        margin-bottom: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.02);
        text-align: left; /* Đảm bảo text luôn căn trái */
    }
    .post-card-v2:hover {
        background: rgba(255, 255, 255, 0.05);
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    .action-btn-v2 {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        color: var(--secondary-text);
        padding: 8px 12px;
        border-radius: 12px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .action-btn-v2:hover {
        background: rgba(0,0,0,0.04);
        color: var(--text-color);
        border-color: rgba(0,0,0,0.1);
    }
    [data-theme="dark"] .post-card-v2 {
        border-color: rgba(255,255,255,0.2); /* Trong chế độ tối, viền đen sẽ mờ hơn hoặc chuyển sang trắng nhẹ để dễ nhìn */
    }
    [data-theme="dark"] .action-btn-v2:hover {
        background: rgba(255,255,255,0.08);
    }
</style>
