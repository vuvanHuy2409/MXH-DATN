@extends('layouts.admin')

@section('title', 'Báo cáo vi phạm')
@section('breadcrumb', 'Báo cáo vi phạm')

@section('content')
    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <div class="adm-card" style="padding: 20px;">
            <p style="font-size: 12px; color: var(--text-faint); margin: 0 0 4px 0;">Báo cáo chờ xử lý</p>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin: 0;">{{ $stats['pending'] }}</h3>
            <p style="font-size: 11px; color: var(--danger); font-weight: 600; margin: 4px 0 0 0;">Cần giải quyết</p>
        </div>
        <div class="adm-card" style="padding: 20px;">
            <p style="font-size: 12px; color: var(--text-faint); margin: 0 0 4px 0;">Bài viết bị báo cáo</p>
            <h3 style="font-size: 24px; font-weight: 700; color: var(--accent); margin: 0;">{{ $stats['post_reports'] }}</h3>
        </div>
        <div class="adm-card" style="padding: 20px;">
            <p style="font-size: 12px; color: var(--text-faint); margin: 0 0 4px 0;">Bình luận bị báo cáo</p>
            <h3 style="font-size: 24px; font-weight: 700; color: #7C3AED; margin: 0;">{{ $stats['comment_reports'] }}</h3>
        </div>
        <div class="adm-card" style="padding: 20px;">
            <p style="font-size: 12px; color: var(--text-faint); margin: 0 0 4px 0;">Tin nhắn bị báo cáo</p>
            <h3 style="font-size: 24px; font-weight: 700; color: #F59E0B; margin: 0;">{{ $stats['message_reports'] }}</h3>
        </div>
    </div>

    <!-- Reason Stats -->
    <div class="adm-card" style="margin-bottom: 24px; padding: 20px;">
        <h4 style="font-size: 13px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0;">Thống kê theo lý do báo cáo</h4>
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 12px;">
            @foreach($reasons as $key => $reason)
            @php $cnt = $reasonStats[$key] ?? 0; $maxVal = max(array_values($reasonStats) ?: [1]); $pct = $maxVal > 0 ? round(($cnt / $maxVal) * 100) : 0; @endphp
            <div style="text-align: center; cursor: pointer; padding: 8px; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background='transparent'" onclick="window.location='{{ route('admin.reports.index', array_merge(request()->query(), ['reason' => $key]) ) }}'">
                <div style="width: 100%; background: var(--surface-2); border-radius: 9999px; height: 6px; margin-bottom: 8px; overflow: hidden;">
                    <div style="height: 100%; border-radius: 9999px; transition: width 0.5s; width: {{ $pct }}%; background: {{ $reason['color'] }};"></div>
                </div>
                <div style="font-size: 18px; font-weight: 700; color: {{ $reason['color'] }}; margin-bottom: 2px;">{{ $cnt }}</div>
                <div style="font-size: 10px; color: var(--text-muted); font-weight: 600; line-height: 1.2;">{{ Str::limit($reason['label'], 18) }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Filters -->
    <div class="adm-card" style="margin-bottom: 24px; padding: 16px;">
        <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 16px; width: 100%;">
            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-faint); text-transform: uppercase;">Loại nội dung</label>
                <select name="type" class="adm-input">
                    <option value="">Tất cả</option>
                    <option value="post" {{ request('type') == 'post' ? 'selected' : '' }}>Bài viết</option>
                    <option value="comment" {{ request('type') == 'comment' ? 'selected' : '' }}>Bình luận</option>
                    <option value="message" {{ request('type') == 'message' ? 'selected' : '' }}>Tin nhắn</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-faint); text-transform: uppercase;">Lý do</label>
                <select name="reason" class="adm-input">
                    <option value="">Tất cả lý do</option>
                    @foreach($reasons as $key => $reason)
                    <option value="{{ $key }}" {{ request('reason') == $key ? 'selected' : '' }}>{{ $reason['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Trạng thái luôn là Chờ xử lý (Pending) -->

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-faint); text-transform: uppercase;">Lọc theo ngày</label>
                <input type="date" name="date" value="{{ request('date') }}" class="adm-input">
            </div>

            <div style="display: flex; flex-direction: column; gap: 4px;">
                <label style="font-size: 11px; font-weight: 700; color: var(--text-faint); text-transform: uppercase;">Lọc theo tháng</label>
                <input type="month" name="month" value="{{ request('month') }}" class="adm-input">
            </div>

            <div style="display: flex; align-items: center; gap: 8px; padding-bottom: 2px;">
                <button type="submit" style="background: var(--accent); color: white; font-size: 13px; font-weight: 600; padding: 7px 16px; border-radius: 8px; border: none; cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">Lọc dữ liệu</button>
                <a href="{{ route('admin.reports.index') }}" style="color: var(--text-muted); font-size: 13px; font-weight: 500; padding: 7px 16px; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--text-primary)'" onmouseout="this.style.color='var(--text-muted)'">Xóa lọc</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="adm-card" style="overflow: hidden;">
        <table class="adm-table" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Loại</th>
                    <th>Nội dung bị báo cáo</th>
                    <th>Lý do</th>
                    <th style="text-align: center;">Số BC</th>
                    <th>Báo cáo gần nhất</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($groupedReports as $row)
                @php
                    $contentPreview = null;
                    $images = collect();
                    if ($row->content) {
                        $contentPreview = \Illuminate\Support\Str::limit($row->content->content ?? '', 70);
                        if ($row->type === 'post' && $row->content->media) {
                            $images = $row->content->media->where('media_type', 'image')->map(function($m) {
                                return $m->media_url;
                            });
                        } elseif ($row->type === 'comment' && $row->content->image_url) {
                            $images = collect([$row->content->image_url]);
                        } elseif ($row->type === 'message' && $row->content->message_type === 'image' && $row->content->content) {
                            $images = collect([$row->content->content]);
                        }
                    }
                @endphp
                <tr>
                    {{-- Loại --}}
                    <td style="white-space: nowrap;">
                        @if($row->type === 'post')
                            <span class="badge" style="background: rgba(59,91,219,0.1); color: #3B5BDB;">Bài viết</span>
                        @elseif($row->type === 'comment')
                            <span class="badge" style="background: rgba(124,58,237,0.1); color: #7C3AED;">Bình luận</span>
                        @else
                            <span class="badge" style="background: rgba(245,158,11,0.1); color: #F59E0B;">Tin nhắn</span>
                        @endif
                    </td>

                    {{-- Nội dung --}}
                    <td style="max-width: 220px;">
                        @if($row->content)
                            <div style="font-size: 12.5px; font-weight: 500; color: var(--text-primary); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $contentPreview }}">
                                {{ $contentPreview ?: '[Không có nội dung văn bản]' }}
                            </div>
                            @if($images->isNotEmpty())
                                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px;">
                                    @foreach($images as $imgUrl)
                                        <div style="position: relative; width: 56px; height: 56px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border); background: var(--surface-2); cursor: pointer; transition: transform 0.15s, box-shadow 0.15s;" 
                                             onclick="openAdminLightbox('{{ asset($imgUrl) }}')"
                                             onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';" 
                                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none';">
                                            <img src="{{ asset($imgUrl) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Thumbnail">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div style="font-size: 11px; color: var(--text-faint); margin-top: 4px;">ID #{{ $row->reported_id }}</div>
                        @else
                            <div style="display: flex; align-items: center; gap: 6px; color: var(--danger);">
                                <span style="font-size: 12px; font-weight: 600;">Nội dung đã bị xóa</span>
                            </div>
                            <div style="font-size: 11px; color: var(--text-faint); margin-top: 2px;">ID #{{ $row->reported_id }}</div>
                        @endif
                    </td>

                    {{-- Lý do (tất cả lý do trong group) --}}
                    <td>
                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                            @foreach($row->reasons_array as $rkey)
                            @php $ri = $reasons[$rkey] ?? ['label' => $rkey, 'color' => '#6b7280']; @endphp
                            <span style="display: inline-flex; padding: 2px 8px; border-radius: 9999px; font-size: 10.5px; font-weight: 600; background: {{ $ri['color'] }}18; color: {{ $ri['color'] }};">
                                {{ $ri['label'] }}
                            </span>
                            @endforeach
                        </div>
                    </td>

                    {{-- Số lần báo cáo --}}
                    <td style="text-align: center;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; font-size: 13px; font-weight: 800;
                            {{ $row->report_count >= 5 ? 'background: rgba(239,68,68,0.1); color: var(--danger);' : ($row->report_count >= 2 ? 'background: rgba(245,158,11,0.1); color: #F59E0B;' : 'background: var(--surface-2); color: var(--text-muted);') }}">
                            {{ $row->report_count }}
                        </span>
                    </td>

                    {{-- Ngày gửi gần nhất --}}
                    <td style="color: var(--text-muted); white-space: nowrap; font-size: 12.5px;">
                        {{ \Carbon\Carbon::parse($row->latest_report_at)->format('d/m/Y H:i') }}
                    </td>

                    {{-- Thao tác --}}
                    <td style="text-align: right;">
                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                            {{-- Xóa nội dung --}}
                            <form action="{{ route('admin.reports.destroyContent') }}" method="POST"
                                onsubmit="return confirm('Xóa nội dung này và đánh dấu {{ $row->report_count }} báo cáo là đã xử lý?');" style="margin:0;">
                                @csrf
                                <input type="hidden" name="type" value="{{ $row->type }}">
                                <input type="hidden" name="reported_id" value="{{ $row->reported_id }}">
                                <button type="submit" class="btn-danger" title="Xóa nội dung vi phạm">
                                    Xóa nội dung
                                </button>
                            </form>
                            {{-- Bỏ qua --}}
                            <form action="{{ route('admin.reports.ignore') }}" method="POST" style="margin:0;">
                                @csrf
                                <input type="hidden" name="type" value="{{ $row->type }}">
                                <input type="hidden" name="reported_id" value="{{ $row->reported_id }}">
                                <button type="submit" class="btn-muted" title="Bỏ qua tất cả báo cáo">
                                    Bỏ qua
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 64px 24px; text-align: center; color: var(--text-faint);">
                        <p style="font-weight: 600; margin: 0;">Không có báo cáo vi phạm nào</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($groupedReports->hasPages())
        <div style="padding: 16px 24px; background: var(--surface-2); border-top: 1px solid var(--border);">
            {{ $groupedReports->links() }}
        </div>
        @endif
    </div>

    <!-- Lightbox Modal -->
    <div id="adminLightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.85); z-index: 99999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s ease;">
        <button onclick="closeAdminLightbox()" style="position: absolute; top: 20px; right: 20px; background: transparent; border: none; color: white; font-size: 36px; cursor: pointer; line-height: 1; padding: 10px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">&times;</button>
        <img id="lightboxImage" src="" style="max-width: 90%; max-height: 90%; object-fit: contain; border-radius: 6px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); border: 2px solid rgba(255,255,255,0.1);">
    </div>
@endsection

@section('scripts')
<script>
function openAdminLightbox(url) {
    const lightbox = document.getElementById('adminLightbox');
    const img = document.getElementById('lightboxImage');
    img.src = url;
    lightbox.style.display = 'flex';
    setTimeout(() => {
        lightbox.style.opacity = '1';
    }, 10);
}

function closeAdminLightbox() {
    const lightbox = document.getElementById('adminLightbox');
    lightbox.style.opacity = '0';
    setTimeout(() => {
        lightbox.style.display = 'none';
        document.getElementById('lightboxImage').src = '';
    }, 250);
}

document.getElementById('adminLightbox').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAdminLightbox();
    }
});
</script>
@endsection

