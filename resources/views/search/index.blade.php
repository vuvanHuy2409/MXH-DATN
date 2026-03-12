@extends('layouts.app')

@section('content')
<div style="padding: 10px 0;">
    <!-- Search Bar Section -->
    <div style="padding: 15px 20px 20px;">
        <form action="{{ route('search') }}" method="GET" id="searchForm">
            <div style="position: relative;">
                <input type="text" name="q" value="{{ $query }}" placeholder="Tên, mã sinh viên, email..." 
                       class="search-input-v2"
                       autocomplete="off" autofocus>
                <div style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--secondary-text); opacity: 0.5;">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
            </div>
            <input type="hidden" name="type" id="searchType" value="{{ $type }}">
        </form>
    </div>

    @if($query)
        <!-- Modern Segmented Control (Tabs) -->
        <div style="padding: 0 20px 15px;">
            <div class="tabs-container-v2">
                <div onclick="setSearchType('all')" class="tab-pill-v2 {{ $type === 'all' ? 'active' : '' }}">Tất cả</div>
                <div onclick="setSearchType('people')" class="tab-pill-v2 {{ $type === 'people' ? 'active' : '' }}">Người dùng</div>
                <div onclick="setSearchType('posts')" class="tab-pill-v2 {{ $type === 'posts' ? 'active' : '' }}">Bài viết</div>
            </div>
        </div>

        <!-- Results Area -->
        <div style="padding: 0 10px;">
            @if(($type === 'all' || $type === 'people') && $users->isNotEmpty())
                <div style="padding: 10px 15px 5px;">
                    <h3 style="font-size: 13px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 0.5px;">Mọi người</h3>
                </div>
                @foreach($users as $u)
                    @include('search._user_item', ['user' => $u])
                @endforeach
            @endif

            @if(($type === 'all' || $type === 'posts') && $posts->isNotEmpty())
                <div style="padding: 20px 15px 5px;">
                    <h3 style="font-size: 13px; font-weight: 800; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 0.5px;">Bài viết</h3>
                </div>
                @foreach($posts as $post)
                    @include('posts._item', ['post' => $post, 'prefix' => 's'])
                @endforeach
            @endif

            @if($users->isEmpty() && $posts->isEmpty())
                <div style="padding: 100px 20px; text-align: center;">
                    <div style="opacity: 0.2; margin-bottom: 15px;">
                        <svg viewBox="0 0 24 24" width="64" height="64" stroke="currentColor" stroke-width="1.5" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <div style="color: var(--secondary-text); font-weight: 600;">Không tìm thấy kết quả cho "{{ $query }}"</div>
                </div>
            @endif
        </div>
    @else
        <!-- Aesthetic Suggestions Section -->
        <div style="padding: 10px 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 850; color: var(--text-color); margin: 0; letter-spacing: -0.5px;">Gợi ý cho bạn</h3>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 4px;">
                @foreach($suggestions as $suggestUser)
                    @include('search._user_item', ['user' => $suggestUser])
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
    .search-input-v2 {
        width: 100%; 
        padding: 16px 20px 16px 52px; 
        border-radius: 20px; 
        border: 1px solid var(--glass-border); 
        background: rgba(0,0,0,0.03); 
        color: var(--text-color); 
        font-size: 16px; 
        outline: none; 
        box-sizing: border-box;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 600;
    }
    .search-input-v2:focus {
        background: var(--glass-bg);
        border-color: var(--accent-color);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05), 0 0 0 4px rgba(0,113,227,0.1);
        transform: translateY(-1px);
    }

    .tabs-container-v2 {
        display: flex; 
        background: rgba(0,0,0,0.04); 
        padding: 4px; 
        border-radius: 16px;
        gap: 4px;
    }
    [data-theme="dark"] .tabs-container-v2 {
        background: rgba(255,255,255,0.05);
    }

    .tab-pill-v2 {
        flex: 1; 
        text-align: center; 
        padding: 10px; 
        cursor: pointer; 
        font-weight: 700; 
        font-size: 14px; 
        color: var(--secondary-text);
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .tab-pill-v2:hover {
        color: var(--text-color);
    }
    .tab-pill-v2.active {
        background: white;
        color: var(--text-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    [data-theme="dark"] .tab-pill-v2.active {
        background: rgba(255,255,255,0.1);
        color: white;
    }
</style>

<script>
    function setSearchType(type) {
        document.getElementById('searchType').value = type;
        document.getElementById('searchForm').submit();
    }
</script>
@endsection
