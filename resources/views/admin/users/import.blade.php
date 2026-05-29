@extends('layouts.admin')

@section('title', 'Import tài khoản')
@section('breadcrumb', 'Import tài khoản')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Cột 1: Hướng dẫn & File Import -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-[16px] font-bold text-[#111827] mb-4 flex items-center gap-2">
                    <i data-lucide="help-circle" class="w-5 h-5 text-[#3B5BDB]"></i>
                    Hướng dẫn viết file CSV
                </h3>
                <div class="space-y-4 text-[13px] text-[#374151] leading-relaxed">
                    <p>Hệ thống hỗ trợ import hàng loạt tài khoản qua file <strong>.csv</strong>. Vui lòng định dạng file theo cấu trúc sau:</p>
                    <div class="bg-gray-900 text-gray-300 p-4 rounded-lg font-mono text-[11px] overflow-x-auto">
                        # Cho Sinh viên:<br>
                        username,email,full_name,student_id,dob(yyyy-mm-dd),class,faculty_id<br><br>
                        # Cho Giảng viên:<br>
                        username,email,full_name,faculty_id
                    </div>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Mật khẩu mặc định:</strong> <span class="text-[#3B5BDB] font-bold">123456@</span></li>
                        <li><strong>Email:</strong> Phải là email duy nhất. Sinh viên nên dùng email trường.</li>
                        <li><strong>Faculty ID:</strong> ID của khoa (VD: 1 - CNTT).</li>
                    </ul>
                    <div class="pt-4 flex flex-wrap gap-3">
                        <a href="{{ route('admin.users.import.template.student') }}" class="flex items-center gap-2 bg-blue-50 text-[#3B5BDB] px-4 py-2 rounded-lg font-bold hover:bg-blue-100 transition-all border border-blue-100">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Mẫu Sinh viên
                        </a>
                        <a href="{{ route('admin.users.import.template.teacher') }}" class="flex items-center gap-2 bg-purple-50 text-[#7C3AED] px-4 py-2 rounded-lg font-bold hover:bg-purple-100 transition-all border border-purple-100">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Mẫu Giảng viên
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-[16px] font-bold text-[#111827] mb-4 flex items-center gap-2">
                    <i data-lucide="file-up" class="w-5 h-5 text-[#3B5BDB]"></i>
                    Tải lên file Import
                </h3>
                <form action="{{ route('admin.users.import.bulk') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[11px] font-bold text-[#9CA3AF] uppercase mb-2">Loại tài khoản</label>
                        <select name="user_type" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none focus:ring-2 focus:ring-[#3B5BDB]/20" required>
                            <option value="student">Sinh viên</option>
                            <option value="teacher">Giảng viên</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-[11px] font-bold text-[#9CA3AF] uppercase mb-2">Chọn file CSV</label>
                        <input type="file" name="csv_file" accept=".csv" class="w-full text-[13px] text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-blue-50 file:text-[#3B5BDB] hover:file:bg-blue-100" required>
                    </div>
                    <button type="submit" class="w-full bg-[#3B5BDB] text-white py-2.5 rounded-lg font-bold text-[13px] hover:bg-[#2F49B8] shadow-sm transition-all flex items-center justify-center gap-2">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i> BẮT ĐẦU IMPORT
                    </button>
                </form>
            </div>
        </div>

        <!-- Cột 2: Thêm đơn lẻ -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-[16px] font-bold text-[#111827] mb-4 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-5 h-5 text-[#7C3AED]"></i>
                Thêm tài khoản đơn lẻ
            </h3>
            
            <div id="roleSelection" class="space-y-4">
                <p class="text-[13px] text-[#6B7280]">Vui lòng chọn loại tài khoản bạn muốn tạo:</p>
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="showForm('student')" class="p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-[#3B5BDB] hover:bg-blue-50/50 transition-all text-center group">
                        <i data-lucide="graduation-cap" class="w-8 h-8 mx-auto mb-2 text-gray-400 group-hover:text-[#3B5BDB]"></i>
                        <span class="block font-bold text-[13px] text-[#374151]">Sinh viên</span>
                    </button>
                    <button onclick="showForm('teacher')" class="p-4 border-2 border-dashed border-gray-200 rounded-xl hover:border-[#7C3AED] hover:bg-purple-50/50 transition-all text-center group">
                        <i data-lucide="briefcase" class="w-8 h-8 mx-auto mb-2 text-gray-400 group-hover:text-[#7C3AED]"></i>
                        <span class="block font-bold text-[13px] text-[#374151]">Giảng viên</span>
                    </button>
                </div>
            </div>

            <form id="singleImportForm" action="{{ route('admin.users.import.single') }}" method="POST" class="hidden space-y-4 mt-4">
                @csrf
                <input type="hidden" name="user_type" id="selectedUserType">
                
                <div class="flex items-center justify-between">
                    <h4 id="formTitle" class="text-[13px] font-bold text-[#111827]">Thêm Sinh viên</h4>
                    <button type="button" onclick="resetRole()" class="text-[11px] text-[#3B5BDB] font-medium hover:underline">Thay đổi loại</button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-[#9CA3AF] uppercase block mb-1">Username</label>
                        <input type="text" name="username" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none" required>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-[#9CA3AF] uppercase block mb-1">Email</label>
                        <input type="email" name="email" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none" required>
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-[#9CA3AF] uppercase block mb-1">Họ và tên</label>
                    <input type="text" name="full_name" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none" required>
                </div>

                <div id="studentFields" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold text-[#9CA3AF] uppercase block mb-1">Mã sinh viên</label>
                            <input type="text" name="student_id" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-[#9CA3AF] uppercase block mb-1">Ngày sinh</label>
                            <input type="date" name="dob" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-[#9CA3AF] uppercase block mb-1">Lớp</label>
                        <input type="text" name="class" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-[#9CA3AF] uppercase block mb-1">Khoa (Faculty ID)</label>
                    <select name="faculty_id" class="w-full p-2 bg-gray-50 border border-gray-200 rounded-lg text-[13px] outline-none">
                        @foreach(\App\Models\Faculty::all() as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg text-[12px] text-blue-700 flex gap-2">
                    <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
                    <span>Mật khẩu mặc định sẽ là <strong>123456@</strong></span>
                </div>

                <button type="submit" class="w-full bg-[#111827] text-white py-2.5 rounded-lg font-bold text-[13px] hover:bg-black transition-all">
                    TẠO TÀI KHOẢN
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function showForm(type) {
            document.getElementById('roleSelection').classList.add('hidden');
            document.getElementById('singleImportForm').classList.remove('hidden');
            document.getElementById('selectedUserType').value = type;
            
            const studentFields = document.getElementById('studentFields');
            const formTitle = document.getElementById('formTitle');
            
            if (type === 'student') {
                studentFields.classList.remove('hidden');
                formTitle.innerText = 'Thêm Sinh viên';
            } else {
                studentFields.classList.add('hidden');
                formTitle.innerText = 'Thêm Giảng viên';
            }
        }

        function resetRole() {
            document.getElementById('roleSelection').classList.remove('hidden');
            document.getElementById('singleImportForm').classList.add('hidden');
        }
    </script>
@endsection
