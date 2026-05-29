<?php

namespace App\Rules;

use App\Services\ToxicDetectionService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ToxicContent implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $service = app(ToxicDetectionService::class);
        $violation = $service->validateContent($value);

        if ($violation) {
            if (isset($violation['error'])) {
                $fail("Hệ thống kiểm duyệt đang tạm dừng hoạt động. Vui lòng thử lại sau.");
                return;
            }

            $label = $violation['label'];
            $reason = ($label === 'HATE') ? 'ngôn ngữ thù ghét' : 'ngôn ngữ xúc phạm';
            
            $message = "Nội dung của bạn bị chặn vì vi phạm tiêu chuẩn cộng đồng ({$reason}).";
            
            if (!empty($violation['banned_word'])) {
                $message .= " (Phát hiện từ cấm: " . $violation['banned_word'] . ")";
            }
            
            $fail($message);
        }
    }
}
