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
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#9CA3AF]"></i>
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
                        <span class="flex items-center gap-1"><i data-lucide="alert-octagon" class="w-4 h-4"></i> Tài khoản có dấu hiệu vi phạm</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('admin.users.toggle-flag', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-green-50 text-green-600 px-3 py-1.5 rounded-lg text-[11px] font-bold hover:bg-green-100 transition-all flex items-center gap-1">
                                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i> KHÔI PHỤC
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO TỐI CAO: Hành động này sẽ XÓA VĨNH VIỄN tài khoản này và TOÀN BỘ dữ liệu (Bài viết, Tin nhắn, Bình luận...). Không thể khôi phục. Bạn có chắc chắn không?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-lg text-[11px] font-bold hover:bg-red-100 transition-all flex items-center gap-1">
                                    <i data-lucide="user-minus" class="w-3 h-3"></i> XÓA VĨNH VIỄN
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-[#9CA3AF]">
                        <i data-lucide="user-check" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
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
