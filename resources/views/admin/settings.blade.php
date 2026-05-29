@extends('layouts.admin')

@section('title', 'Cài đặt hệ thống')
@section('breadcrumb', 'Cài đặt hệ thống')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="settings-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Cấu hình API Kiểm duyệt</h1>
                    <p class="text-sm text-gray-500">Quản lý kết nối tới dịch vụ Toxic Detection</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- URL API -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i data-lucide="globe" class="w-4 h-4 text-gray-400"></i>
                        Địa chỉ URL API
                    </label>
                    <input type="text" name="toxic_detector_url" id="toxic_detector_url" 
                        value="{{ $toxicUrl }}" 
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm"
                        placeholder="{{ $defaultUrl }}">
                    <p class="text-[11px] text-gray-400">Địa chỉ máy chủ chạy dịch vụ AI (mặc định: {{ $defaultUrl }})</p>
                </div>

                <!-- PORT API -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i data-lucide="hash" class="w-4 h-4 text-gray-400"></i>
                        Cổng kết nối (Port)
                    </label>
                    <input type="number" name="toxic_detector_port" id="toxic_detector_port" 
                        value="{{ $toxicPort }}" 
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all outline-none text-sm"
                        placeholder="{{ $defaultPort }}">
                    <p class="text-[11px] text-gray-400">Cổng dịch vụ Python (mặc định: {{ $defaultPort }})</p>
                </div>
            </div>

            <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100 mb-8">
                <div class="flex items-start gap-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 mt-1">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-blue-900 mb-1">Kiểm tra kết nối</h3>
                        <p class="text-xs text-blue-700 mb-4">Hãy thử kiểm tra xem hệ thống có thể kết nối tới API với cấu hình trên hay không trước khi lưu.</p>
                        
                        <div id="test-result" class="hidden mb-4 p-3 rounded-lg text-xs font-medium"></div>

                        <button type="button" id="btn-test" onclick="testApiConnection()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-blue-200 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-50 transition-all shadow-sm">
                            <i data-lucide="play-circle" class="w-4 h-4"></i>
                            Bắt đầu kiểm tra
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-50">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    Lưu cấu hình
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function testApiConnection() {
    const btn = document.getElementById('btn-test');
    const resultDiv = document.getElementById('test-result');
    const url = document.getElementById('toxic_detector_url').value;
    const port = document.getElementById('toxic_detector_port').value;

    if (!url || !port) {
        alert('Vui lòng nhập đầy đủ URL và Port');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Đang kiểm tra...';
    lucide.createIcons();

    resultDiv.classList.add('hidden');

    fetch("{{ route('admin.settings.test') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ url, port })
    })
    .then(res => res.json())
    .then(data => {
        resultDiv.classList.remove('hidden');
        resultDiv.classList.remove('bg-green-100', 'text-green-700', 'bg-red-100', 'text-red-700');
        
        if (data.status === 'success') {
            resultDiv.classList.add('bg-green-100', 'text-green-700');
            resultDiv.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i> ' + data.message;
        } else {
            resultDiv.classList.add('bg-red-100', 'text-red-700');
            resultDiv.innerHTML = '<i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i> ' + data.message;
        }
        lucide.createIcons();
    })
    .catch(err => {
        resultDiv.classList.remove('hidden');
        resultDiv.classList.add('bg-red-100', 'text-red-700');
        resultDiv.innerHTML = 'Lỗi kết nối: ' + err.message;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="play-circle" class="w-4 h-4"></i> Bắt đầu kiểm tra';
        lucide.createIcons();
    });
}
</script>
@endsection
