@extends('layouts.admin')

@section('title', 'Quản lý tài khoản')
@section('breadcrumb', 'Tất cả tài khoản')

@section('content')
    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-[12px] text-[#9CA3AF] mb-1 uppercase font-bold tracking-wider">Tổng tài khoản</p>
            <h3 class="text-2xl font-bold text-[#111827]">{{ number_format($stats['total']) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-[12px] text-[#9CA3AF] mb-1 uppercase font-bold tracking-wider">Sinh viên</p>
            <h3 class="text-2xl font-bold text-[#3B5BDB]">{{ number_format($stats['students']) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-[12px] text-[#9CA3AF] mb-1 uppercase font-bold tracking-wider">Giảng viên</p>
            <h3 class="text-2xl font-bold text-[#7C3AED]">{{ number_format($stats['teachers']) }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-[12px] text-[#9CA3AF] mb-1 uppercase font-bold tracking-wider">Bị đánh dấu</p>
            <h3 class="text-2xl font-bold text-[#EF4444]">{{ number_format($stats['flagged']) }}</h3>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[300px]">
                <label class="text-[11px] font-bold text-[#9CA3AF] uppercase mb-1 block">Tìm kiếm</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên, Email, Username..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none focus:bg-white focus:ring-2 focus:ring-[#3B5BDB]/20 transition-all">
                </div>
            </div>
            <div>
                <label class="text-[11px] font-bold text-[#9CA3AF] uppercase mb-1 block">Ngày sinh (SV)</label>
                <input type="date" name="dob" value="{{ request('dob') }}" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none focus:bg-white focus:ring-2 focus:ring-[#3B5BDB]/20 transition-all">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-[#3B5BDB] text-white px-6 py-2 rounded-lg text-[13px] font-semibold hover:bg-[#2F49B8] shadow-sm transition-all">Lọc kết quả</button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-[#6B7280] text-[13px] font-medium hover:text-[#111827] transition-all">Xóa lọc</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
            <thead class="bg-[#F9FAFB] text-[11px] uppercase text-[#6B7280] font-bold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Người dùng</th>
                    <th class="px-6 py-4">Loại / Khoa</th>
                    <th class="px-6 py-4">Ngày sinh</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-[13px]">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center font-bold text-[11px] text-[#3B5BDB]">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    {{ substr($user->username, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-[#111827]">{{ $user->student->full_name ?? $user->teacher->full_name ?? $user->username }}</div>
                                <div class="text-[11px] text-[#9CA3AF]">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-0.5">
                            @if($user->user_type === 'teacher')
                                <span class="text-[#7C3AED] font-semibold">Giảng viên</span>
                                <span class="text-[11px] text-[#6B7280]">{{ $user->teacher->faculty->name ?? 'N/A' }}</span>
                            @else
                                <span class="text-[#3B5BDB] font-semibold">Sinh viên</span>
                                <span class="text-[11px] text-[#6B7280]">{{ $user->student->faculty->name ?? 'N/A' }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-[#6B7280]">
                        {{ $user->student->dob ? \Carbon\Carbon::parse($user->student->dob)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($user->status === 'flagged')
                            <span class="bg-red-50 text-red-600 px-2 py-0.5 rounded-full text-[11px] font-bold">BỊ ĐÁNH DẤU</span>
                        @else
                            <span class="bg-green-50 text-green-600 px-2 py-0.5 rounded-full text-[11px] font-bold">HOẠT ĐỘNG</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('admin.users.toggle-flag', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 {{ $user->status === 'flagged' ? 'text-gray-400 hover:text-green-600' : 'text-gray-400 hover:text-red-600' }} transition-colors cursor-pointer" title="{{ $user->status === 'flagged' ? 'Gỡ đánh dấu' : 'Đánh dấu tài khoản' }}">
                                    @if($user->status === 'flagged')
                                        <!-- Shield Check SVG -->
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                            <path d="m9 11 2 2 4-4"></path>
                                        </svg>
                                    @else
                                        <!-- Flag SVG -->
                                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline">
                                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                            <line x1="4" y1="22" x2="4" y2="15"></line>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Hành động này sẽ XÓA HOÀN TOÀN tài khoản, bài viết, tin nhắn, bình luận và tất cả dữ liệu của người dùng này. Bạn có chắc chắn không?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors cursor-pointer" title="Xóa vĩnh viễn">
                                    <!-- Trash SVG -->
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-gray-50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
@endsection
