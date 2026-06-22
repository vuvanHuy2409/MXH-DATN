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

                <!-- Các phản hồi (Dữ liệu đã được gom phẳng từ Controller) -->
                @if(!empty($reply->all_flat_replies))
                    <div class="nested-replies" id="nested-container-{{ $reply->id }}">
                        @php
                            $allFlatReplies = $reply->all_flat_replies;
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
    @php
        $isMemberOfGroup = true;
        if($post->group_id) {
            $isMemberOfGroup = \App\Models\GroupMember::where('group_id', $post->group_id)->where('user_id', auth()->id())->exists();
        }
    @endphp

    @if($isMemberOfGroup)
    <div style="position: sticky; bottom: 0; background: var(--glass-bg); backdrop-filter: blur(20px); padding: 15px 20px; border-top: 1px solid var(--glass-border); z-index: 100;">
        <div id="commentImagePreviewContainer" style="display: none; padding: 10px; background: rgba(0,0,0,0.02); border-radius: 12px; margin-bottom: 10px; position: relative; max-width: 650px; margin-left: auto; margin-right: auto;">
            <img id="commentImagePreview" src="" style="max-height: 100px; border-radius: 8px;">
            <span onclick="removeCommentImage()" style="position: absolute; top: 5px; right: 5px; cursor: pointer; background: rgba(0,0,0,0.5); color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;">&times;</span>
        </div>
        <form onsubmit="submitReply(event)" style="max-width: 650px; margin: 0 auto;" enctype="multipart/form-data">
            @csrf
            <!-- Luôn giữ ID của bài viết gốc -->
            <input type="hidden" id="rootPostId" value="{{ $post->id }}">
            <!-- ID của bình luận đang được trả lời (nếu có) -->
            <input type="hidden" id="parentCommentId" value="">

            <div style="display: flex; gap: 12px; align-items: center;">
                <div class="avatar" style="width: 35px; height: 35px; background-image: url('{{ auth()->user()->avatar_url }}'); background-size: cover; flex-shrink: 0;"></div>
                <div id="commentInputWrapper" style="flex-grow: 1; background: rgba(0,0,0,0.05); border: 1px solid var(--glass-border); border-radius: 20px; padding: 5px 15px; display: flex; align-items: center; gap: 10px; transition: border-color 0.2s;">
                    <input type="text" id="replyContent" name="content" placeholder="Viết câu trả lời..." style="background: transparent; border: none; flex-grow: 1; color: var(--text-color); outline: none; padding: 8px 0; font-size: 14px;" autocomplete="off" oninput="validateCommentInput(); clearCommentError()">
                    <label style="cursor: pointer; opacity: 0.6; display: flex; align-items: center;">
                        <input type="file" id="commentImageInput" name="image" accept="image/*" style="display: none;" onchange="previewCommentImage(this); validateCommentInput()">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    </label>
                </div>
                <button type="submit" id="commentSendBtn" disabled style="background: var(--glass-bg); border: 1.5px solid var(--text-color); color: var(--text-color); padding: 8px 18px; border-radius: 12px; font-weight: 800; font-size: 14px; cursor: not-allowed; opacity: 0.4; transition: all 0.2s; flex-shrink: 0;">
                    Gửi
                </button>
            </div>

            <!-- Thông báo lỗi vi phạm nội dung (ẩn mặc định) -->
            <div id="commentErrorBox" style="display: none; max-width: 650px; margin: 8px auto 0; padding: 10px 14px; background: rgba(255,59,48,0.08); border: 1px solid rgba(255,59,48,0.25); border-left: 3px solid #ff3b30; border-radius: 12px; font-size: 13px; font-weight: 600; color: #ff3b30; align-items: flex-start; gap: 8px;">
                <span style="font-size: 15px; flex-shrink: 0;">⚠️</span>
                <span id="commentErrorText"></span>
            </div>
        </form>
    </div>
    @else
    <div style="position: sticky; bottom: 0; background: var(--glass-bg); backdrop-filter: blur(20px); padding: 20px; border-top: 1px solid var(--glass-border); z-index: 100; text-align: center;">
        <div style="max-width: 650px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <p style="margin: 0; font-size: 14px; font-weight: 600; color: var(--secondary-text);">Bạn cần tham gia cộng đồng này để có thể bình luận.</p>
            <form action="{{ route('groups.join', $post->group->slug) }}" method="POST">
                @csrf
                <button type="submit" class="btn-post" style="padding: 8px 25px; border-radius: 12px; font-size: 14px; font-weight: 800;">Tham gia cộng đồng</button>
            </form>
        </div>
    </div>
    @endif
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
        validateCommentInput();
    }

    function validateCommentInput() {
        const content = document.getElementById('replyContent').value.trim();
        const image = document.getElementById('commentImageInput').files.length > 0;
        const btn = document.getElementById('commentSendBtn');
        
        if (content || image) {
            btn.disabled = false;
            btn.style.cursor = 'pointer';
            btn.style.opacity = '1';
        } else {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            btn.style.opacity = '0.4';
        }
    }

    function previewCommentImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('commentImagePreview').src = e.target.result;
                document.getElementById('commentImagePreviewContainer').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeCommentImage() {
        document.getElementById('commentImageInput').value = '';
        document.getElementById('commentImagePreviewContainer').style.display = 'none';
        document.getElementById('commentImagePreview').src = '';
    }

    function showCommentError(message) {
        const box = document.getElementById('commentErrorBox');
        const text = document.getElementById('commentErrorText');
        const wrapper = document.getElementById('commentInputWrapper');
        text.textContent = message;
        box.style.display = 'flex';
        // Đổi border ô nhập thành màu đỏ
        if (wrapper) wrapper.style.borderColor = '#ff3b30';
        // Đặt lại con trỏ vào cuối ô nhập
        const input = document.getElementById('replyContent');
        input.focus();
        const len = input.value.length;
        input.setSelectionRange(len, len);
        // Hiệu ứng rung nhẹ
        input.style.animation = 'none';
        void input.offsetWidth;
        input.style.animation = 'commentShake 0.4s ease';
    }

    function clearCommentError() {
        const box = document.getElementById('commentErrorBox');
        const wrapper = document.getElementById('commentInputWrapper');
        if (box) box.style.display = 'none';
        if (wrapper) wrapper.style.borderColor = 'var(--glass-border)';
    }

    function submitReply(event) {
        event.preventDefault();
        const contentInput = document.getElementById('replyContent');
        const content = contentInput.value.trim();
        const rootPostId = document.getElementById('rootPostId').value;
        const parentId = document.getElementById('parentCommentId').value;
        const imageFile = document.getElementById('commentImageInput').files[0];

        if (!content && !imageFile) {
            showCommentError('Vui lòng nhập nội dung hoặc chọn ảnh.');
            return;
        }

        clearCommentError();

        const btn = document.getElementById('commentSendBtn');
        btn.disabled = true;
        btn.textContent = '...';

        const formData = new FormData();
        formData.append('content', content);
        if (parentId) formData.append('parent_id', parentId);
        if (imageFile) formData.append('image', imageFile);

        fetch(`/posts/${rootPostId}/reply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        }).then(async res => {
            const data = await res.json().catch(() => ({}));
            if (res.ok) {
                // Thành công: xóa nội dung và reload
                contentInput.value = '';
                removeCommentImage();
                location.reload();
            } else {
                // Lỗi: KHÔNG xóa text, hiển thị thông báo lỗi inline
                const errorMsg = data.errors?.content?.[0]
                    || data.message
                    || 'Nội dung không thể gửi. Vui lòng chỉnh sửa và thử lại.';
                showCommentError(errorMsg);
                btn.disabled = false;
                btn.textContent = 'Gửi';
            }
        }).catch(err => {
            console.error('Error:', err);
            showCommentError('Không thể kết nối máy chủ. Vui lòng thử lại.');
            btn.disabled = false;
            btn.textContent = 'Gửi';
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
        border-color: rgba(255,255,255,0.08) !important;
        background: rgba(22,22,32,0.85) !important;
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

    [data-theme="dark"] .comment-bubble.nested {
        background: rgba(18,18,28,0.8) !important;
        border-color: rgba(255,255,255,0.06) !important;
    }

    [data-theme="dark"] #quickReplyIndicator {
        background: rgba(94,158,255,0.08) !important;
        border-top-color: rgba(255,255,255,0.07) !important;
    }

    [data-theme="dark"] #commentImagePreviewContainer {
        background: rgba(255,255,255,0.03) !important;
    }

    [data-theme="dark"] form[onsubmit="submitReply(event)"] > div > div:nth-child(2) {
        background: rgba(255,255,255,0.05) !important;
        border-color: rgba(255,255,255,0.08) !important;
    }

    [data-theme="dark"] #replyContent {
        color: var(--text-color) !important;
    }

    [data-theme="dark"] #replyContent::placeholder {
        color: rgba(255,255,255,0.3) !important;
    }
</style>
@endsection