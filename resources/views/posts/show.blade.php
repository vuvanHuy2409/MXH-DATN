@extends('layouts.app')

@section('content')
<div style="padding: 10px 0;">
    <!-- Back Button -->
    <div style="padding: 10px 20px; display: flex; align-items: center; gap: 15px;">
        <a href="javascript:history.back()" style="color: var(--text-color); text-decoration: none; display: flex; align-items: center;">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h2 style="margin: 0; font-size: 20px;">Bình Luận</h2>
    </div>

    <!-- Main Post (Bài viết gốc - Lớn) -->
    <div style="border-bottom: 1px solid var(--glass-border);">
        @include('posts._item', ['post' => $post, 'prefix' => 'main'])
    </div>

    <!-- Separator -->
    <div style="padding: 20px 25px 10px; display: flex; align-items: center; gap: 10px; text-align: left;">
        <span style="font-size: 13px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px;">Bình luận</span>
        <div style="flex-grow: 1; height: 1px; background: var(--glass-border); opacity: 0.3;"></div>
    </div>

    <!-- Replies List (Danh sách bình luận - Nhỏ hơn) -->
    <div id="replies-container" style="padding: 10px 0;">
        @php
            // Nhóm bình luận theo parent_id
            $commentsByParent = $post->comments->groupBy('parent_id');
            $rootComments = $commentsByParent->get(null) ?? collect();

            // Hàm thu thập tất cả bình luận con thành mảng phẳng
            if (!function_exists('getFlatReplies')) {
                function getFlatReplies($parentId, $commentsByParent, &$allReplies, $depth = 1) {
                    if ($depth > 100) return;
                    $replies = $commentsByParent->get($parentId) ?? collect();
                    foreach($replies as $reply) {
                        $allReplies[] = $reply;
                        getFlatReplies($reply->id, $commentsByParent, $allReplies, $depth + 1);
                    }
                }
            }
        @endphp

        @foreach($rootComments as $reply)
            <div class="comment-section">
                <!-- Bình luận gốc (Cấp 1) -->
                @include('posts._item', [
                    'post' => $reply,
                    'prefix' => 'r1-' . $reply->id,
                    'hideLike' => false,
                    'hideRepost' => true,
                    'hideShare' => true,
                    'hideReply' => true,
                    'isComment' => true,
                    'small' => true,
                    'class' => 'comment-bubble'
                ])

                <!-- Các phản hồi -->
                @if($commentsByParent->has($reply->id))
                    <div class="nested-replies" id="nested-container-{{ $reply->id }}">
                        @php
                            $allFlatReplies = [];
                            getFlatReplies($reply->id, $commentsByParent, $allFlatReplies);
                            $firstReply = array_shift($allFlatReplies);
                        @endphp
                        
                        <!-- Hiển thị bình luận đầu tiên -->
                        <div class="comment-section" style="margin-bottom: 5px; padding: 0;">
                            @include('posts._item', [
                                'post' => $firstReply,
                                'prefix' => 'r-flat-' . $firstReply->id,
                                'hideLike' => false,
                                'hideRepost' => true,
                                'hideShare' => true,
                                'hideReply' => true,
                                'isComment' => true,
                                'small' => true,
                                'class' => 'comment-bubble nested'
                            ])
                        </div>

                        <!-- Nếu còn nhiều hơn 1 bình luận con, ẩn phần còn lại -->
                        @if(count($allFlatReplies) > 0)
                            <div id="more-replies-{{ $reply->id }}" style="display: none;">
                                @foreach($allFlatReplies as $hiddenReply)
                                    <div class="comment-section" style="margin-bottom: 5px; padding: 0;">
                                        @include('posts._item', [
                                            'post' => $hiddenReply,
                                            'prefix' => 'r-flat-' . $hiddenReply->id,
                                            'hideLike' => false,
                                            'hideRepost' => true,
                                            'hideShare' => true,
                                            'hideReply' => true,
                                            'isComment' => true,
                                            'small' => true,
                                            'class' => 'comment-bubble nested'
                                        ])
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="show-more-comments" onclick="toggleReplies({{ $reply->id }})" id="btn-more-{{ $reply->id }}" style="font-size: 13px; font-weight: 700; color: var(--accent-color); cursor: pointer; padding: 5px 0 10px 0; display: flex; align-items: center; gap: 8px; opacity: 0.9;">
                                <div style="width: 15px; height: 2px; background: currentColor; opacity: 0.3; border-radius: 1px;"></div>
                                <span>Xem thêm {{ count($allFlatReplies) }} bình luận</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Quick Reply Form (Form phản hồi nhanh luôn ở dưới) -->
    <div id="quickReplyIndicator" style="display: none; padding: 10px 20px; background: rgba(0,113,227,0.05); font-size: 12px; border-top: 1px solid var(--glass-border); position: sticky; bottom: 70px; z-index: 101; backdrop-filter: blur(10px);">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 650px; margin: 0 auto;">
            <span>Đang trả lời <strong>@<span id="quickReplyUser"></span></strong></span>
            <span onclick="cancelQuickReply()" style="cursor: pointer; opacity: 0.5; font-size: 18px;">&times;</span>
        </div>
    </div>
    <div style="position: sticky; bottom: 0; background: var(--glass-bg); backdrop-filter: blur(20px); padding: 15px 20px; border-top: 1px solid var(--glass-border); z-index: 100;">
        <form onsubmit="submitReply(event)" style="max-width: 650px; margin: 0 auto;">
            @csrf
            <!-- Luôn giữ ID của bài viết gốc -->
            <input type="hidden" id="rootPostId" value="{{ $post->id }}">
            <!-- ID của bình luận đang được trả lời (nếu có) -->
            <input type="hidden" id="parentCommentId" value="">

            <div style="display: flex; gap: 12px; align-items: center;">
                <div class="avatar" style="width: 35px; height: 35px; background-image: url('{{ auth()->user()->avatar_url }}'); background-size: cover; flex-shrink: 0;"></div>
                <div style="flex-grow: 1; background: rgba(0,0,0,0.05); border: 1px solid var(--glass-border); border-radius: 20px; padding: 5px 15px;">
                    <input type="text" id="replyContent" placeholder="Viết câu trả lời..." style="background: transparent; border: none; width: 100%; color: var(--text-color); outline: none; padding: 8px 0; font-size: 14px;" required autocomplete="off">
                </div>
                <button type="submit" class="btn-post" style="padding: 6px 15px; font-size: 13px;">Gửi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function prepareQuickReply(id, username) {
        document.getElementById('parentCommentId').value = id;
        document.getElementById('quickReplyUser').innerText = username;
        document.getElementById('quickReplyIndicator').style.display = 'block';
        document.getElementById('replyContent').focus();
        document.getElementById('replyContent').placeholder = "Trả lời @" + username + "...";
    }

    function cancelQuickReply() {
        document.getElementById('parentCommentId').value = '';
        document.getElementById('quickReplyIndicator').style.display = 'none';
        document.getElementById('replyContent').placeholder = "Viết câu trả lời...";
    }

    function submitReply(event) {
        event.preventDefault();
        const content = document.getElementById('replyContent').value.trim();
        const rootPostId = document.getElementById('rootPostId').value;
        const parentId = document.getElementById('parentCommentId').value;

        if (!content) return;

        const formData = new FormData();
        formData.append('content', content);
        if (parentId) {
            formData.append('parent_id', parentId);
        }

        fetch(`/posts/${rootPostId}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        }).then(res => {
            if (res.ok) {
                document.getElementById('replyContent').value = '';
                location.reload();
            } else {
                return res.json().then(data => {
                    throw data;
                });
            }
        }).catch(err => {
            console.error('Error:', err);
            alert('Có lỗi xảy ra: ' + (err.message || 'Không thể gửi bình luận'));
        });
    }

    function toggleLike(postId) {
        const btns = document.querySelectorAll(`.like-btn[data-post-id="${postId}"]`);
        const token = '{{ csrf_token() }}';
        
        btns.forEach(btn => {
            const countSpan = btn.querySelector('.like-count');
            const svg = btn.querySelector('svg');
            const isCurrentlyLiked = btn.classList.contains('liked');
            let currentCount = parseInt(countSpan.innerText) || 0;

            // 1. UI Update
            if (isCurrentlyLiked) {
                btn.classList.remove('liked');
                countSpan.innerText = Math.max(0, currentCount - 1);
                if (svg) svg.setAttribute('fill', 'none');
            } else {
                btn.classList.add('liked');
                countSpan.innerText = currentCount + 1;
                if (svg) svg.setAttribute('fill', 'currentColor');
                
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle-effect';
                sparkle.style.left = '50%';
                sparkle.style.top = '50%';
                sparkle.style.transform = 'translate(-50%, -50%)';
                btn.appendChild(sparkle);
                setTimeout(() => sparkle.remove(), 500);
            }

            // 2. Animation
            if (svg) {
                svg.classList.remove('like-animate');
                void svg.offsetWidth;
                svg.classList.add('like-animate');
            }
        });

        fetch(`/posts/${postId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btns.forEach(btn => {
                const span = btn.querySelector('.like-count');
                if (span) span.innerText = data.count;
            });
        });
    }

    function toggleRepost(postId) {
        const btns = document.querySelectorAll(`.repost-btn[data-post-id="${postId}"]`);
        const token = '{{ csrf_token() }}';
        btns.forEach(btn => {
            const countSpan = btn.querySelector('.repost-count');
            const isReposted = btn.classList.contains('reposted');
            if (isReposted) {
                btn.classList.remove('reposted');
                btn.style.color = 'inherit';
                countSpan.innerText = Math.max(0, parseInt(countSpan.innerText) - 1);
            } else {
                btn.classList.add('reposted');
                btn.style.color = '#00c300';
                countSpan.innerText = parseInt(countSpan.innerText) + 1;
            }
        });
        fetch(`/posts/${postId}/repost`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json()).then(data => {
                btns.forEach(btn => {
                    if (btn.querySelector('.repost-count')) btn.querySelector('.repost-count').innerText = data.count;
                });
            });
    }

    function toggleDropdown(id) {
        const dropdown = document.getElementById("dropdown-" + id);
        if (dropdown) dropdown.classList.toggle("show");
    }

    function deletePost(id) {
        if (confirm('Xóa bài viết này?')) fetch(`/posts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => history.back());
    }

    function deleteComment(id) {
        if (confirm('Xóa bình luận này?')) fetch(`/comments/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(() => location.reload());
    }

    function sharePost(id) {
        navigator.clipboard.writeText(window.location.origin + '/posts/' + id);
        alert('Đã sao chép liên kết!');
    }

    function toggleReplies(id) {
        const moreDiv = document.getElementById('more-replies-' + id);
        const btn = document.getElementById('btn-more-' + id);
        if (moreDiv.style.display === 'none') {
            moreDiv.style.display = 'block';
            btn.querySelector('span').innerText = 'Thu gọn';
        } else {
            moreDiv.style.display = 'none';
            // Đếm số lượng con (đã render trong moreDiv)
            const count = moreDiv.querySelectorAll('.comment-section').length;
            btn.querySelector('span').innerText = 'Xem thêm ' + count + ' bình luận';
        }
    }
</script>

<style>
    .comment-section {
        margin-bottom: 8px;
        padding: 0 15px;
    }

    .comment-bubble {
        background: rgba(0, 0, 0, 0.02) !important;
        border: 1px solid rgba(0,0,0,0.1) !important;
        border-radius: 20px !important;
        margin-bottom: 8px !important;
        padding: 10px 16px !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .comment-bubble div[style*="font-size: 15px"] {
        font-size: 14px !important;
    }

    [data-theme="dark"] .comment-bubble {
        border-color: rgba(255,255,255,0.2) !important;
    }

    .nested-replies {
        margin-left: 55px;
        border-left: 2px solid rgba(0,0,0,0.05);
        padding-left: 15px;
    }

    [data-theme="dark"] .nested-replies {
        border-left-color: rgba(255,255,255,0.1);
    }

    .comment-bubble.nested {
        background: rgba(0, 0, 0, 0.01) !important;
        border: 1px solid rgba(0,0,0,0.3) !important;
        padding: 8px 14px !important;
        border-radius: 16px !important;
    }
</style>
@endsection