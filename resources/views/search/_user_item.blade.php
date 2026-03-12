<div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; border-radius: 18px; transition: all 0.2s ease;" onmouseover="this.style.background='rgba(0,0,0,0.03)'" onmouseout="this.style.background='transparent'">
    <div style="display: flex; align-items: center; gap: 14px;">
        <a href="{{ route('profile.show', $user->username) }}" style="position: relative;">
            <div class="avatar" style="background-image: url('{{ $user->avatar_url ?: asset('/avatars/user.png') }}'); background-size: cover; width: 52px; height: 52px; border-radius: 50%; border: 1.5px solid var(--glass-border); box-shadow: 0 4px 12px rgba(0,0,0,0.05);"></div>
            @if($user->user_type === 'teacher')
                <div style="position: absolute; bottom: 0; right: 0; background: #FFD700; color: #000; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; font-size: 10px; font-weight: 900;">✓</div>
            @endif
        </a>
        <div style="display: flex; flex-direction: column;">
            <a href="{{ route('profile.show', $user->username) }}" style="text-decoration: none; color: var(--text-color);">
                <div style="font-weight: 800; font-size: 15.5px; letter-spacing: -0.2px;">{{ $user->full_name ?? $user->username }}</div>
                @if($user->full_name)
                    <div style="font-size: 13px; color: var(--secondary-text); margin-top: -2px; font-weight: 500;">@<span>{{ $user->username }}</span></div>
                @endif
            </a>
            <div style="color: var(--secondary-text); font-size: 12px; margin-top: 2px; font-weight: 500; opacity: 0.8;">{{ $user->followers_count ?? 0 }} người theo dõi</div>
        </div>
    </div>
    
    @if(auth()->id() !== $user->id)
        @php $isFollowing = auth()->user()->following->contains($user->id); @endphp
        <form action="{{ route('users.follow', $user) }}" method="POST">
            @csrf
            <button type="submit" style="background: {{ $isFollowing ? 'transparent' : 'var(--text-color)' }}; border: {{ $isFollowing ? '1.5px solid var(--glass-border)' : 'none' }}; color: {{ $isFollowing ? 'var(--text-color)' : 'white' }}; padding: 8px 18px; border-radius: 12px; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s ease; min-width: 100px;">
                {{ $isFollowing ? 'Đang theo dõi' : ($user->follows_me ? 'Theo dõi lại' : 'Theo dõi') }}
            </button>
        </form>
    @endif
</div>
