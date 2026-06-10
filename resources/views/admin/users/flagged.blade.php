@extends('layouts.admin')

@section('title', 'Tài khoản bị đánh dấu')
@section('breadcrumb', 'Tài khoản bị đánh dấu')

@section('content')
    <!-- Search -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('admin.users.flagged') }}" method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="text-[11px] font-bold text-[#9CA3AF] uppercase mb-1 block">Tìm kiếm tài khoản vi phạm</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nhập tên hoặc email..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none focus:bg-white focus:ring-2 focus:ring-[#3B5BDB]/20 transition-all">
                </div>
            </div>
            <button type="submit" class="bg-[#EF4444] text-white px-6 py-2 rounded-lg text-[13px] font-semibold hover:bg-red-600 shadow-sm transition-all">Tìm kiếm</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
            <thead class="bg-[#F9FAFB] text-[11px] uppercase text-[#6B7280] font-bold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Người dùng</th>
                    <th class="px-6 py-4">Loại</th>
                    <th class="px-6 py-4">Lý do đánh dấu</th>
                    <th class="px-6 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-[13px]">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center font-bold text-[11px] text-red-600">
                                {{ substr($user->username, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-[#111827]">{{ $user->student->full_name ?? $user->teacher->full_name ?? $user->username }}</div>
                                <div class="text-[11px] text-[#9CA3AF]">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="{{ $user->user_type === 'teacher' ? 'text-[#7C3AED]' : 'text-[#3B5BDB]' }} font-semibold uppercase text-[11px]">
                            {{ $user->user_type === 'teacher' ? 'Giảng viên' : 'Sinh viên' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-red-500 font-medium">
                        <span class="flex items-center gap-1.5">
                            <!-- Alert Octagon SVG -->
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline">
                                <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            Tài khoản có dấu hiệu vi phạm
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('admin.users.toggle-flag', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-50 text-green-600 px-3 py-1.5 rounded-lg text-[11px] font-bold hover:bg-green-100 transition-all flex items-center gap-1 cursor-pointer">
                                    <!-- Rotate CCW SVG -->
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline">
                                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                        <polyline points="3 3 3 8 8 8"></polyline>
                                    </svg>
                                    KHÔI PHỤC
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO TỐI CAO: Hành động này sẽ XÓA VĨNH VIỄN tài khoản này và TOÀN BỘ dữ liệu (Bài viết, Tin nhắn, Bình luận...). Không thể khôi phục. Bạn có chắc chắn không?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-[11px] font-bold hover:bg-red-100 transition-all flex items-center gap-1 cursor-pointer">
                                    <!-- User Minus SVG -->
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <line x1="22" y1="11" x2="16" y2="11"></line>
                                    </svg>
                                    XÓA VĨNH VIỄN
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-[#9CA3AF]">
                        <!-- User Check SVG -->
                        <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-3 opacity-20">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <polyline points="17 11 19 13 23 9"></polyline>
                        </svg>
                        <p>Hiện không có tài khoản nào bị đánh dấu vi phạm</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="p-4 bg-gray-50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
@endsection
