@extends('layouts.admin')

@section('title', 'Báo cáo vi phạm')
@section('breadcrumb', 'Báo cáo vi phạm')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-[12px] text-[#9CA3AF] mb-1">Tổng báo cáo</p>
            <h3 class="text-2xl font-bold text-[#111827]">{{ $stats['total'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-[12px] text-[#9CA3AF] mb-1">Bài viết bị báo cáo</p>
            <h3 class="text-2xl font-bold text-[#3B5BDB]">{{ $stats['post_reports'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-[12px] text-[#9CA3AF] mb-1">Bình luận bị báo cáo</p>
            <h3 class="text-2xl font-bold text-[#7C3AED]">{{ $stats['comment_reports'] }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <p class="text-[12px] text-[#9CA3AF] mb-1">Tin nhắn bị báo cáo</p>
            <h3 class="text-2xl font-bold text-[#F59E0B]">{{ $stats['message_reports'] }}</h3>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap items-center gap-4">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-center gap-4 w-full">
            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-bold text-[#9CA3AF] uppercase">Loại nội dung</label>
                <select name="type" class="text-[13px] border border-gray-200 rounded-lg px-3 py-1.5 outline-none bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3B5BDB]/20 transition-all">
                    <option value="">Tất cả</option>
                    <option value="post" {{ request('type') == 'post' ? 'selected' : '' }}>Bài viết</option>
                    <option value="comment" {{ request('type') == 'comment' ? 'selected' : '' }}>Bình luận</option>
                    <option value="message" {{ request('type') == 'message' ? 'selected' : '' }}>Tin nhắn</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-bold text-[#9CA3AF] uppercase">Lọc theo ngày</label>
                <input type="date" name="date" value="{{ request('date') }}" class="text-[13px] border border-gray-200 rounded-lg px-3 py-1.5 outline-none bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3B5BDB]/20 transition-all">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-[11px] font-bold text-[#9CA3AF] uppercase">Lọc theo tháng</label>
                <input type="month" name="month" value="{{ request('month') }}" class="text-[13px] border border-gray-200 rounded-lg px-3 py-1.5 outline-none bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#3B5BDB]/20 transition-all">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-[#3B5BDB] text-white text-[13px] font-medium px-4 py-1.5 rounded-lg hover:bg-[#2F49B8] transition-colors shadow-sm">Lọc dữ liệu</button>
                <a href="{{ route('admin.reports.index') }}" class="text-[#6B7280] text-[13px] font-medium px-4 py-1.5 hover:text-[#111827]">Xóa lọc</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
            <thead class="bg-[#F9FAFB] text-[11px] uppercase text-[#6B7280] font-bold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Người báo cáo</th>
                    <th class="px-6 py-4">Loại</th>
                    <th class="px-6 py-4">Lý do</th>
                    <th class="px-6 py-4">Ngày gửi</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-[13px]">
                @forelse($reports as $report)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-[#111827]">{{ $report->reporter->username ?? 'Ẩn danh' }}</div>
                        <div class="text-[11px] text-[#9CA3AF]">{{ $report->reporter->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($report->type === 'post')
                            <span class="bg-blue-50 text-[#3B5BDB] px-2 py-0.5 rounded-full text-[11px] font-semibold">Bài viết</span>
                        @elseif($report->type === 'comment')
                            <span class="bg-purple-50 text-[#7C3AED] px-2 py-0.5 rounded-full text-[11px] font-semibold">Bình luận</span>
                        @else
                            <span class="bg-orange-50 text-[#F59E0B] px-2 py-0.5 rounded-full text-[11px] font-semibold">Tin nhắn</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-[#111827] font-medium">{{ $report->reason }}</div>
                        <div class="text-[11px] text-[#6B7280] max-w-[200px] truncate" title="{{ $report->details }}">{{ $report->details }}</div>
                    </td>
                    <td class="px-6 py-4 text-[#6B7280]">
                        {{ $report->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($report->status === 'pending')
                            <span class="text-orange-500 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Chờ xử lý</span>
                        @elseif($report->status === 'resolved')
                            <span class="text-green-500 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Đã xóa</span>
                        @else
                            <span class="text-gray-400 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Đã bỏ qua</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('admin.reports.destroyContent', $report->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nội dung bị báo cáo này?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Xóa nội dung">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.reports.ignore', $report->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors" title="Bỏ qua báo cáo">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-[#9CA3AF]">
                        <i data-lucide="shield-check" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                        <p>Không có báo cáo vi phạm nào được tìm thấy</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($reports->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $reports->links() }}
        </div>
        @endif
    </div>
@endsection
