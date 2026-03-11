@extends('layouts.app')

@section('content')
<!-- Create Post Trigger -->
<div class="glass-bubble" style="margin-top: 20px; padding: 15px 25px; cursor: pointer; display: flex; align-items: center; gap: 15px; border-radius: 24px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onclick="openModal()" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--glass-shadow)'">
    <div class="avatar" style="background-image: url('{{ auth()->user()->avatar_url }}'); background-size: cover; width: 44px; height: 42px; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.05);"></div>
    <div style="color: var(--secondary-text); font-size: 16px; font-weight: 500; flex-grow: 1; opacity: 0.7;">Có gì mới hôm nay, {{ explode(' ', auth()->user()->studentDetail->full_name ?? auth()->user()->username)[0] }}?</div>
    <div style="background: var(--accent-color); color: white; padding: 8px 18px; border-radius: 12px; font-weight: 700; font-size: 14px; box-shadow: 0 8px 20px rgba(0, 113, 227, 0.2);">Đăng</div>
</div>

<!-- Navigation Tabs -->
<div style="display: flex; gap: 5px; margin: 30px 0 20px; background: rgba(0,0,0,0.03); padding: 5px; border-radius: 18px; width: fit-content;">
    <div id="tab-foryou" onclick="switchTab('foryou')" class="tab-item active">
        Dành cho bạn
    </div>
    <div id="tab-following" onclick="switchTab('following')" class="tab-item">
        Đang theo dõi
    </div>
</div>

<style>
    .tab-item {
        padding: 8px 24px;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        border-radius: 14px;
        transition: all 0.3s ease;
        color: var(--secondary-text);
    }
    .tab-item.active {
        background: white;
        color: var(--text-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    [data-theme="dark"] .tab-item.active {
        background: var(--glass-bg);
        color: white;
    }
</style>

<!-- Tab Content: Dành cho bạn -->
<div id="content-foryou">
    @forelse($posts as $post)
        @include('posts._item', ['post' => $post, 'prefix' => 'fy'])
    @empty
    <p style="text-align: center; padding: 50px; opacity: 0.5;">Chưa có bài viết nào.</p>
    @endforelse
</div>

<!-- Tab Content: Đang theo dõi -->
<div id="content-following" style="display: none;">
    @forelse($followingPosts as $post)
        @include('posts._item', ['post' => $post, 'prefix' => 'fl'])
    @empty
    <p style="text-align: center; padding: 50px; opacity: 0.5;">Theo dõi thêm bạn bè để xem bài viết.</p>
    @endforelse
</div>

<!-- SIDE PANEL FOR COMMENTS -->
<div id="commentSidePanel" class="comment-modal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.2); backdrop-filter: blur(10px); justify-content: flex-end;">
    <div class="glass-bubble" style="width: 100%; max-width: 500px; height: 100%; border-radius: 35px 0 0 35px; display: flex; flex-direction: column; overflow: hidden; animation: slideLeft 0.4s cubic-bezier(0.16, 1, 0.3, 1);">
        <!-- Header -->
        <div style="padding: 25px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02);">
            <h3 style="margin: 0; font-size: 20px; font-weight: 800;">Bình luận</h3>
            <div onclick="closeCommentSidePanel()" style="cursor: pointer; width: 36px; height: 36px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.1)'" onmouseout="this.style.background='rgba(0,0,0,0.05)'">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </div>
        </div>

        <!-- Source Post Info (Mini) -->
        <div style="padding: 20px 25px; background: rgba(0,0,0,0.02); display: flex; gap: 12px; align-items: flex-start; border-bottom: 1px solid var(--glass-border);">
            <div id="panelSourceAvatar" class="avatar" style="width: 32px; height: 32px; background-size: cover; border-radius: 50%; flex-shrink: 0;"></div>
            <div style="flex-grow: 1; min-width: 0;">
                <div id="panelSourceUsername" style="font-weight: 800; font-size: 14px; margin-bottom: 2px;"></div>
                <div id="panelSourceContent" style="font-size: 13px; opacity: 0.8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
            </div>
        </div>

        <!-- Comments List -->
        <div id="panelActualComments" style="flex-grow: 1; overflow-y: auto; padding: 25px; display: flex; flex-direction: column; gap: 5px;">
            <!-- Comments will be rendered here -->
        </div>

        <!-- Image Preview for Panel -->
        <div id="panelImagePreviewContainer" style="display: none; padding: 10px 25px; background: rgba(0,0,0,0.02); position: relative;">
            <img id="panelImagePreview" src="" style="max-height: 80px; border-radius: 8px;">
            <span onclick="removePanelImage()" style="position: absolute; top: 5px; right: 30px; cursor: pointer; background: rgba(0,0,0,0.5); color: white; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">&times;</span>
        </div>

        <!-- Reply Indicator -->
        <div id="panelReplyIndicator" style="display: none; padding: 10px 25px; background: rgba(0,113,227,0.05); border-top: 1px solid var(--glass-border); align-items: center; justify-content: space-between;">
            <div style="font-size: 12px; font-weight: 700; color: var(--accent-color);">Đang trả lời <span id="panelReplyUser"></span></div>
            <div onclick="cancelPanelReply()" style="cursor: pointer; opacity: 0.5;"><svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></div>
        </div>

        <!-- Input Area -->
        <div style="padding: 20px 25px 40px; border-top: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(20px);">
            <div style="display: flex; gap: 12px; align-items: center; background: rgba(0,0,0,0.03); border: 1px solid var(--glass-border); border-radius: 24px; padding: 8px 18px;">
                <div class="avatar" style="background-image: url('{{ auth()->user()->avatar_url }}'); background-size: cover; width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;"></div>
                <input type="text" id="panelCommentInput" placeholder="Viết bình luận..." style="flex-grow: 1; background: transparent; border: none; outline: none; padding: 10px 0; font-size: 14px; color: var(--text-color);">
                <label style="cursor: pointer; opacity: 0.6; display: flex; align-items: center;">
                    <input type="file" id="panelCommentImageInput" accept="image/*" style="display: none;" onchange="previewPanelImage(this)">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </label>
                <button onclick="submitPanelComment()" style="background: none; border: none; color: var(--accent-color); font-weight: 800; cursor: pointer; padding: 5px 10px; font-size: 14px;">Đăng</button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slideLeft {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    .author-badge {
        background: var(--accent-color);
        color: white;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 6px;
        text-transform: uppercase;
        margin-left: 8px;
    }
    .comment-thread-line {
        position: absolute;
        width: 2px;
        background: var(--glass-border);
        opacity: 0.4;
        border-radius: 1px;
    }
</style>

<script>
    function closeShareModal(e) {
        if (e.target.id === 'shareModal') document.getElementById('shareModal').style.display = 'none';
    }

    let activePanelPostId = null;
    let activePanelAuthorId = null;
    let activeParentCommentId = null;

    function openCommentSidePanel(postId, username, content, avatar, authorId) {
        activePanelPostId = postId;
        activePanelAuthorId = authorId;
        activeParentCommentId = postId;
        document.getElementById('panelSourceUsername').innerText = username;
        document.getElementById('panelSourceContent').innerText = content;
        document.getElementById('panelSourceAvatar').style.backgroundImage = `url('${avatar}')`;
        document.getElementById('panelActualComments').innerHTML = '<p style="text-align: center; opacity: 0.5; padding: 20px;">Đang tải...</p>';
        document.getElementById('commentSidePanel').style.display = 'flex';
        document.body.classList.add('modal-open');
        cancelPanelReply();
        fetch(`/posts/${postId}/comments`)
            .then(res => res.json())
            .then(comments => {
                const list = document.getElementById('panelActualComments');
                list.innerHTML = '';
                if (comments.length === 0) {
                    list.innerHTML = '<p style="text-align: center; opacity: 0.4; padding: 20px;">Chưa có bình luận nào.</p>';
                } else {
                    comments.forEach(c => list.appendChild(createPanelCommentElement(c)));
                }
            });
    }

    function closeCommentSidePanel() { 
        document.getElementById('commentSidePanel').style.display = 'none'; 
        document.body.classList.remove('modal-open');
    }

    function createPanelCommentElement(c) {
        const div = document.createElement('div');
        div.className = 'comment-item';
        const isNested = c.parent_id && c.parent_id != activePanelPostId;
        div.style.cssText = `display: flex; gap: 12px; position: relative; margin-left: ${isNested ? '45px' : '0px'}; margin-bottom: 15px; background: rgba(0,0,0,0.02); padding: 15px; border-radius: 22px; border: 1px solid rgba(0,0,0,0.1); flex-direction: column;`;
        const authorBadge = c.user_id === activePanelAuthorId ? '<span class="author-badge">Tác giả</span>' : '';
        const imageHtml = c.image_url ? `
            <div style="margin-top: 10px; border-radius: 12px; overflow: hidden; border: 1px solid var(--glass-border); max-width: 200px;">
                <img src="${c.image_url}" style="width: 100%; display: block; cursor: pointer;" onclick="openLightbox('${c.image_url}')">
            </div>
        ` : '';
        
        div.innerHTML = `
            <div style="display: flex; gap: 12px;">
                <div class="avatar" style="width: 34px; height: 34px; background-image: url(\'${c.user.avatar_url}\'); background-size: cover; flex-shrink: 0; z-index: 2; border-radius: 50%; border: 1px solid rgba(0,0,0,0.1);"></div>
                <div style="flex-grow: 1; z-index: 2;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                        <div style="display: flex; align-items: center;">
                            <strong style="font-size: 13.5px; font-weight: 750;">${c.user.username}</strong>
                            ${authorBadge}
                        </div>
                        <span style="font-size: 11px; opacity: 0.5;">${new Date(c.created_at).toLocaleDateString()}</span>
                    </div>
                    <div style="font-size: 14px; line-height: 1.5; color: var(--text-color);">${escapeHtml(c.content)}</div>
                    ${imageHtml}
                    <div style="margin-top: 8px; display: flex; gap: 15px; align-items: center;">
                        <span onclick="preparePanelReply(${c.id}, \'${c.user.username}\')" style="font-size: 12px; font-weight: 700; color: var(--accent-color); cursor: pointer; opacity: 0.8; transition: opacity 0.2s;" onmouseover="this.style.opacity=\'1\'" onmouseout="this.style.opacity=\'0.8\'">Trả lời</span>
                    </div>
                </div>
            </div>
        `;
        return div;
    }

    function previewPanelImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('panelImagePreview').src = e.target.result;
                document.getElementById('panelImagePreviewContainer').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePanelImage() {
        document.getElementById('panelCommentImageInput').value = '';
        document.getElementById('panelImagePreviewContainer').style.display = 'none';
        document.getElementById('panelImagePreview').src = '';
    }

    function preparePanelReply(id, user) {
        activeParentCommentId = id;
        document.getElementById('panelReplyUser').innerText = '@' + user;
        document.getElementById('panelReplyIndicator').style.display = 'flex';
        document.getElementById('panelCommentInput').focus();
    }

    function cancelPanelReply() {
        activeParentCommentId = activePanelPostId;
        document.getElementById('panelReplyIndicator').style.display = 'none';
    }

    function submitPanelComment() {
        const input = document.getElementById('panelCommentInput');
        const content = input.value.trim();
        const imageFile = document.getElementById('panelCommentImageInput').files[0];

        if (!content && !imageFile) return;

        const formData = new FormData();
        formData.append('content', content);
        if (imageFile) {
            formData.append('image', imageFile);
        }
        if (activeParentCommentId && activeParentCommentId != activePanelPostId) {
            formData.append('parent_id', activeParentCommentId);
        }

        fetch(`/posts/${activePanelPostId}/reply`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData
        }).then(res => res.json()).then(reply => {
            input.value = ''; 
            removePanelImage();
            cancelPanelReply();
            const list = document.getElementById('panelActualComments');
            if (list.innerText.includes('Chưa có bình luận')) list.innerHTML = '';
            
            // Append at the bottom for chronological order
            list.appendChild(createPanelCommentElement(reply));
            
            // Scroll to bottom
            list.scrollTop = list.scrollHeight;

            document.querySelectorAll(`.comment-count-display[data-post-id="${activePanelPostId}"]`).forEach(el => { el.innerText = parseInt(el.innerText || 0) + 1; });
        });
    }

    function toggleCommentLike(id, el) {
        const countSpan = el.querySelector('.like-count');
        const isLiked = el.classList.contains('liked');
        let count = parseInt(countSpan.innerText) || 0;
        const svg = el.querySelector('svg');
        if (isLiked) {
            el.classList.remove('liked'); el.style.color = 'inherit';
            svg.setAttribute('fill', 'none'); countSpan.innerText = Math.max(0, count - 1);
        } else {
            el.classList.add('liked'); el.style.color = '#ff3b30';
            svg.setAttribute('fill', 'currentColor'); countSpan.innerText = count + 1;
        }
        fetch(`/posts/${id}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
    }

    function toggleLike(postId) {
        const btns = document.querySelectorAll(`.like-btn[data-post-id="${postId}"]`);
        const token = '{{ csrf_token() }}';
        
        btns.forEach(btn => {
            const countSpan = btn.querySelector('.like-count');
            const svg = btn.querySelector('svg');
            const isCurrentlyLiked = btn.classList.contains('liked');
            let currentCount = parseInt(countSpan.innerText) || 0;

            // 1. Cập nhật UI ngay lập tức (Optimistic)
            if (isCurrentlyLiked) {
                btn.classList.remove('liked');
                countSpan.innerText = Math.max(0, currentCount - 1);
                if (svg) svg.setAttribute('fill', 'none');
            } else {
                btn.classList.add('liked');
                countSpan.innerText = currentCount + 1;
                if (svg) svg.setAttribute('fill', 'currentColor');
                
                // Hiệu ứng tia sáng (chỉ khi like)
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle-effect';
                sparkle.style.left = '50%';
                sparkle.style.top = '50%';
                sparkle.style.transform = 'translate(-50%, -50%)';
                btn.appendChild(sparkle);
                setTimeout(() => sparkle.remove(), 500);
            }

            // 2. Chạy Animation nảy tim
            if (svg) {
                svg.classList.remove('like-animate');
                void svg.offsetWidth; // Force reflow
                svg.classList.add('like-animate');
            }
        });

        // 3. Gửi request lên server
        fetch('/posts/' + postId + '/like', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            // Cập nhật lại số lượng chính xác từ server (nếu có sai lệch)
            btns.forEach(btn => {
                const span = btn.querySelector('.like-count');
                if (span) span.innerText = data.count;
            });
        });
    }

    function toggleRepost(id) {
        const btns = document.querySelectorAll(`.repost-btn[data-post-id="${id}"]`);
        btns.forEach(btn => {
            const countSpan = btn.querySelector('.repost-count');
            const isReposted = btn.classList.contains('reposted');
            let count = parseInt(countSpan.innerText) || 0;
            if (isReposted) {
                btn.classList.remove('reposted'); btn.style.color = 'inherit';
                countSpan.innerText = Math.max(0, count - 1);
            } else {
                btn.classList.add('reposted'); btn.style.color = '#00c300';
                countSpan.innerText = count + 1;
            }
        });
        fetch(`/posts/${id}/repost`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })
        .then(res => res.json()).then(data => {
            btns.forEach(btn => { btn.querySelector('.repost-count').innerText = data.count; });
        });
    }

    function switchTab(tab) {
        document.getElementById('content-foryou').style.display = tab === 'foryou' ? 'block' : 'none';
        document.getElementById('content-following').style.display = tab === 'following' ? 'block' : 'none';
        
        const t1 = document.getElementById('tab-foryou');
        const t2 = document.getElementById('tab-following');
        
        if (tab === 'foryou') {
            t1.classList.add('active');
            t2.classList.remove('active');
        } else {
            t2.classList.add('active');
            t1.classList.remove('active');
        }
    }

    function toggleDropdown(id) {
        const dropdown = document.getElementById("dropdown-" + id);
        if (dropdown) dropdown.classList.toggle("show");
    }
    
    function escapeHtml(text) { const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }
    function sharePost(id) { navigator.clipboard.writeText(window.location.origin + '/posts/' + id); alert('Đã sao chép liên kết!'); }
</script>
@endsection