<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a general WhatsApp message to a phone number.
     *
     * @param string $phone
     * @param string $message
     * @return array
     */
    public function sendWhatsApp(string $phone, string $message): array
    {
        $enabled = config('services.fonnte.enabled', false);
        $token = config('services.fonnte.token');
        $url = config('services.fonnte.url', 'https://api.fonnte.com/send');

        // Clean phone number format (replace leading '0' with '62')
        $cleanPhone = $phone;
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        Log::info("Attempting to send WhatsApp notification to {$cleanPhone}: \"{$message}\"");

        if (!$enabled) {
            Log::info("WhatsApp Gateway is disabled. Skipping API call.");
            return [
                'success' => false,
                'status' => 'disabled',
                'message' => 'API is disabled. Logged successfully.'
            ];
        }

        if (empty($token)) {
            Log::warning("WhatsApp token is empty. Cannot dispatch notification.");
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'API token is missing.'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post($url, [
                'target' => $cleanPhone,
                'message' => $message
            ]);

            $body = $response->json();
            $statusCode = $response->status();

            if ($response->successful() && ($body['status'] ?? false)) {
                Log::info("WhatsApp successfully sent to {$cleanPhone}. Gateway response: " . json_encode($body));
                return [
                    'success' => true,
                    'status' => 'sent',
                    'response' => $body
                ];
            }

            Log::error("WhatsApp Gateway API call failed. Status: {$statusCode}. Body: " . json_encode($body));
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $body['reason'] ?? 'Unknown gateway error'
            ];
        } catch (\Exception $e) {
            Log::error("Exception occurred while sending WhatsApp: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'exception',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to a specific Member and flash to session for UI popup.
     *
     * @param Member $member
     * @param string $title
     * @param string $message
     * @return bool
     */
    public function sendMemberNotification(Member $member, string $title, string $message): bool
    {
        // 1. Get member's phone number
        $phone = $member->no_hp;

        // 2. Fetch user's name
        $name = $member->user->name ?? 'Anggota';
        $formattedMessage = "Halo {$name},\n\n{$message}";

        $result = ['success' => false];

        if (!empty($phone)) {
            $result = $this->sendWhatsApp($phone, $formattedMessage);
        } else {
            Log::warning("Member ID {$member->id} has no phone number (no_hp) configured. Skipping WhatsApp call.");
        }

        // 3. Flash to session so the premium widget overlay continues to render in browser
        session()->flash('sms_notification', [
            'title' => $title,
            'message' => $message,
            'phone' => $phone ?? 'Tidak dikonfigurasi',
            'status' => $result['status'] ?? 'skipped'
        ]);

        return $result['success'] ?? false;
    }
}
