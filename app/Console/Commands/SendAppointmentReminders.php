<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:remind';
    protected $description = 'Gửi tin nhắn nhắc nhở khi lịch hẹn đến giờ';

    public function handle()
    {
        $tz = 'Asia/Ho_Chi_Minh';
        $now = Carbon::now($tz);
        $windowStart = $now->copy()->subSeconds(59);
        $windowEnd = $now->copy()->addSeconds(59);

        $appointments = Appointment::where('status', 'upcoming')
            ->whereBetween('appointment_time', [$windowStart, $windowEnd])
            ->with(['conversation', 'creator'])
            ->get();

        foreach ($appointments as $appointment) {
            $formattedTime = Carbon::parse($appointment->appointment_time)->setTimezone($tz)->format('H:i d/m/Y');
            $locationHtml = $appointment->location
                ? '<div style="font-size:13px;margin-top:4px;opacity:0.8;">📍 ' . e($appointment->location) . '</div>'
                : '';

            $content = '<div style="font-weight:700;font-size:14px;margin-bottom:4px;">' . e($appointment->title) . '</div>'
                . '<div style="font-size:13px;opacity:0.8;">🕐 ' . $formattedTime . '</div>'
                . $locationHtml;

            Message::create([
                'conversation_id' => $appointment->conversation_id,
                'sender_id'       => $appointment->creator_id,
                'message_type'    => 'system',
                'content'         => $content,
                'metadata'        => ['appointment_id' => $appointment->id, 'type' => 'appointment_due'],
            ]);

            $appointment->update(['status' => 'completed']);
            $appointment->conversation->touch();
        }

        $this->info("Đã xử lý {$appointments->count()} lịch hẹn.");
        return Command::SUCCESS;
    }
}
