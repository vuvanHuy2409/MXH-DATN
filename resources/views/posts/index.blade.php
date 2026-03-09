@extends('layouts.app')

@section('content')
<!-- Trigger Modal -->
<div onclick="openModal()" class="post-card" style="cursor: pointer; display: flex; align-items: center; gap: 15px; margin-top: 20px;">
    <div class="avatar" style="background-image: url('{{ auth()->user()->avatar_url }}'); background-size: cover; width: 42px; height: 42px;"></div>
    <div style="color: var(--secondary-text); font-size: 15px; flex-grow: 1;">Bạn đang nghĩ gì?</div>
    <button class="btn-post" style="opacity: 0.8; font-size: 14px;">Đăng</button>
</div>

<!-- Tabs -->
<div style="display: flex; gap: 15px; margin-bottom: 25px; margin-top: 20px;">
    <div id="tab-foryou" onclick="switchTab('foryou')" style="padding: 8px 20px; cursor: pointer; font-weight: 600; font-size: 15px; border-radius: 20px; background: var(--glass-bg); backdrop-filter: blur(10px); border: 1px solid var(--glass-border); transition: all 0.3s; color: var(--text-color);">
        Dành cho bạn
    </div>
    <div id="tab-following" onclick="switchTab('following')" style="padding: 8px 20px; cursor: pointer; font-weight: 600; font-size: 15px; border-radius: 20px; color: var(--secondary-text); transition: all 0.3s;">
        Đang theo dõi
    </div>
</div>

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
<div id="commentSidePanel" class="comment-modal">

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
        div.style.cssText = `display: flex; gap: 10px; position: relative; margin-left: ${isNested ? '35px' : '0px'}; margin-bottom: 20px;`;
        const authorBadge = c.user_id === activePanelAuthorId ? '<span class="author-badge">Tác giả</span>' : '';
        const isLiked = c.is_liked_by_me;
        div.innerHTML = `
            ${!isNested ? '<div class="comment-thread-line" style="left: 16px; top: 35px; bottom: -15px;"></div>' : ''}
            <div class="avatar" style="width: 32px; height: 32px; background-image: url(\'${c.user.avatar_url}\'); background-size: cover; flex-shrink: 0; z-index: 2; border-radius: 50%;"></div>
            <div style="flex-grow: 1; z-index: 2;">
                <div style="background: rgba(255, 255, 255, 0.06); padding: 10px 14px; border-radius: 18px; border: 1px solid var(--glass-border);">
                    <div style="display: flex; align-items: center; margin-bottom: 2px;">
                        <strong style="font-size: 13px;">${c.user.username}</strong>
                        ${authorBadge}
                    </div>
                    <div style="font-size: 13px; line-height: 1.4; opacity: 0.9;">${escapeHtml(c.content)}</div>
                </div>
                <div style="margin-top: 4px; display: flex; gap: 15px; font-size: 11px; color: var(--secondary-text); padding-left: 5px;">
                    <span onclick="toggleCommentLike(${c.id}, this)" class="comment-like-btn ${isLiked ? 'liked' : ''}" style="cursor: pointer; display: flex; align-items: center; gap: 4px; ${isLiked ? 'color: #ff3b30;' : ''}">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="${isLiked ? 'currentColor' : 'none'}"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                        <span class="like-count">${c.like_count}</span>
                    </span>
                    <span onclick="preparePanelReply(${c.id}, '${c.user.username}')" style="cursor: pointer; font-weight: 600;">Trả lời</span>
                </div>
            </div>
        `;
        return div;
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
        if (!content) return;
        fetch(`/posts/${activeParentCommentId}/reply`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ content: content })
        }).then(res => res.json()).then(reply => {
            input.value = ''; cancelPanelReply();
            const list = document.getElementById('panelActualComments');
            if (list.innerText.includes('Chưa có bình luận')) list.innerHTML = '';
            list.appendChild(createPanelCommentElement(reply));
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
            const isLiked = btn.classList.contains('liked');
            let count = parseInt(countSpan.innerText) || 0;

            if (isLiked) {
                btn.classList.remove('liked'); btn.style.color = 'inherit';
                svg.setAttribute('fill', 'none'); countSpan.innerText = Math.max(0, count - 1);
            } else {
                btn.classList.add('liked'); btn.style.color = '#ff3b30';
                svg.setAttribute('fill', 'currentColor'); countSpan.innerText = count + 1;
            }
        });

        fetch('/posts/' + postId + '/like', { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
        .then(res => res.json()).then(data => {
            btns.forEach(btn => { btn.querySelector('.like-count').innerText = data.count; });
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
        const t1 = document.getElementById('tab-foryou'), t2 = document.getElementById('tab-following');
        if (tab === 'foryou') {
            t1.style.background = 'var(--glass-bg)'; t1.style.color = 'var(--text-color)';
            t2.style.background = 'none'; t2.style.color = 'var(--secondary-text)';
        } else {
            t2.style.background = 'var(--glass-bg)'; t2.style.color = 'var(--text-color)';
            t1.style.background = 'none'; t1.style.color = 'var(--secondary-text)';
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