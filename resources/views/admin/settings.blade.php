@extends('layouts.admin')

@section('title', 'Cài đặt hệ thống')
@section('breadcrumb', 'Cài đặt hệ thống')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="adm-card" style="overflow: hidden; padding: 28px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--accent-bg); color: var(--accent); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </div>
            <div>
                <h1 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0;">Cấu hình API Kiểm duyệt</h1>
                <p style="font-size: 12px; color: var(--text-muted); margin: 4px 0 0 0;">Quản lý kết nối tới dịch vụ AI Toxic Detection</p>
            </div>
        </div>

        <!-- URL API Input -->
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px;">
            <label style="font-size: 13px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-muted);">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
                Địa chỉ URL API
            </label>
            <input type="text" name="toxic_detector_url" id="toxic_detector_url" 
                value="{{ $toxicUrl }}" 
                class="adm-input"
                style="width: 100%; padding: 10px 14px; font-size: 13.5px;"
                placeholder="Ví dụ: http://127.0.0.1:8000">
            <p style="font-size: 11px; color: var(--text-faint); margin: 0;">Đường dẫn máy chủ chạy dịch vụ AI (Mặc định: http://127.0.0.1:8000)</p>
        </div>

        <!-- Test Connection Box (Auto-saves on success) -->
        <div style="background: var(--surface-2); border: 1px solid var(--border); border-radius: 12px; padding: 16px;">
            <div style="display: flex; align-items: start; gap: 12px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59,91,219,0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px 0;">Kết nối & Lưu cấu hình</h3>
                    <p style="font-size: 11px; color: var(--text-muted); margin: 0 0 12px 0;">Nhấn nút bên dưới để kiểm tra kết nối. Nếu kết nối thành công, hệ thống sẽ tự động lưu cấu hình này làm cấu hình chính thức.</p>
                    
                    <div id="test-result" style="display: none; padding: 10px; border-radius: 8px; font-size: 11.5px; font-weight: 600; margin-bottom: 12px;"></div>

                    <button type="button" id="btn-test" onclick="testApiConnection()" class="btn-muted" style="font-size: 11.5px; padding: 8px 20px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; color: white; background: var(--accent); border: none; border-radius: 8px;">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polygon points="10 8 16 12 10 16 10 8"></polygon>
                        </svg>
                        Kiểm tra & Lưu cấu hình
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function testApiConnection() {
    const btn = document.getElementById('btn-test');
    const resultDiv = document.getElementById('test-result');
    const url = document.getElementById('toxic_detector_url').value;

    if (!url) {
        alert('Vui lòng nhập URL API');
        return;
    }

    btn.disabled = true;
    btn.style.opacity = '0.7';
    btn.innerHTML = `
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" style="display: inline-block; vertical-align: middle; animation: spin 1s linear infinite;">
            <style>
                @keyframes spin { 100% { transform: rotate(360deg); } }
            </style>
            <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.1)"></circle>
            <path d="M12 2a10 10 0 0 1 10 10" stroke="currentColor" stroke-linecap="round"></path>
        </svg> Đang kiểm tra...`;

    resultDiv.style.display = 'none';

    fetch("{{ route('admin.settings.test') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ url: url })
    })
    .then(res => res.json())
    .then(data => {
        resultDiv.style.display = 'block';
        resultDiv.style.background = '';
        resultDiv.style.color = '';
        
        if (data.status === 'success') {
            resultDiv.style.background = 'rgba(34,197,94,0.1)';
            resultDiv.style.color = '#10B981';
            resultDiv.innerHTML = '✓ ' + data.message;
        } else {
            resultDiv.style.background = 'rgba(239,68,68,0.1)';
            resultDiv.style.color = '#EF4444';
            resultDiv.innerHTML = '✕ ' + data.message;
        }
    })
    .catch(err => {
        resultDiv.style.display = 'block';
        resultDiv.style.background = 'rgba(239,68,68,0.1)';
        resultDiv.style.color = '#EF4444';
        resultDiv.innerHTML = '✕ Lỗi kết nối: ' + err.message;
    })
    .finally(() => {
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.innerHTML = `
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="inline">
                <circle cx="12" cy="12" r="10"></circle>
                <polygon points="10 8 16 12 10 16 10 8"></polygon>
            </svg> Kiểm tra & Lưu cấu hình`;
    });
}
</script>
@endsection
