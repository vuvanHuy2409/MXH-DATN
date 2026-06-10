<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validReasons = array_keys(\App\Models\Report::$REASONS);

        $request->validate([
            'reported_id' => 'required|integer',
            'type'        => 'required|string|in:post,comment,message',
            'reason'      => 'required|string|in:' . implode(',', $validReasons),
            'details'     => 'nullable|string|max:500',
        ]);

        // Kiểm tra xem user này đã báo cáo nội dung này chưa
        $alreadyReported = Report::where('user_id', auth()->id())
            ->where('reported_id', $request->reported_id)
            ->where('type', $request->type)
            ->exists();

        // Nếu đã báo cáo rồi: trả về success (UX tốt) nhưng KHÔNG lưu DB
        // → tránh spam báo cáo trùng lặp trong trang quản trị
        if ($alreadyReported) {
            return response()->json([
                'success' => true,
                'message' => 'Báo cáo của bạn đã được ghi nhận. Chúng tôi sẽ xem xét sớm nhất có thể.'
            ]);
        }

        Report::create([
            'user_id'     => auth()->id(),
            'reported_id' => $request->reported_id,
            'type'        => $request->type,
            'reason'      => $request->reason,
            'details'     => $request->details,
            'status'      => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo của bạn đã được gửi thành công. Chúng tôi sẽ xem xét sớm nhất có thể.'
        ]);
    }
}

