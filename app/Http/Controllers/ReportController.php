<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reported_id' => 'required|integer',
            'type' => 'required|string|in:group,post,user',
            'reason' => 'required|string',
            'details' => 'nullable|string',
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'reported_id' => $request->reported_id,
            'type' => $request->type,
            'reason' => $request->reason,
            'details' => $request->details,
        ]);

        return response()->json(['success' => true, 'message' => 'Báo cáo của bạn đã được gửi thành công.']);
    }
}
