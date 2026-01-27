<?php

namespace App\Services;

use Twilio\Rest\Client;
use Twilio\Exceptions\RestException;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Task;

class TwilioService
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        // Initialize Twilio client if credentials are set
        if (config('services.twilio.account_sid') && config('services.twilio.auth_token')) {
            $this->client = new Client(
                config('services.twilio.account_sid'),
                config('services.twilio.auth_token')
            );
            $this->fromNumber = config('services.twilio.from');
        }
    }

    /**
     * Send WhatsApp notification when a task is assigned to a user
     */
    public function sendTaskAssignedNotification(Task $task, User $user): bool
    {
        if (!$this->client || !$user->phone) {
            return false;
        }

        try {
            // Format the WhatsApp number (must start with whatsapp:)
            $toNumber = $this->formatWhatsAppNumber($user->phone);

            // Create message content
            $message = $this->buildTaskAssignedMessage($task, $user);

            // Send WhatsApp message
            $this->client->messages->create(
                $toNumber,
                [
                    'from' => 'whatsapp:' . $this->fromNumber,
                    'body' => $message
                ]
            );

            Log::info("WhatsApp task notification sent", [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'task_id' => $task->id,
                'task_name' => $task->name,
                'to_number' => $toNumber,
            ]);

            return true;
        } catch (RestException $e) {
            Log::error("Twilio WhatsApp error", [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp notification error", [
                'user_id' => $user->id,
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Format phone number for WhatsApp (must include country code and start with whatsapp:)
     */
    protected function formatWhatsAppNumber(string $phone): string
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $phone);

        // If doesn't start with country code, assume US (+1)
        if (strlen($phone) === 10) {
            $phone = '1' . $phone;
        }

        return 'whatsapp:+' . $phone;
    }

    /**
     * Build task assignment message
     */
    protected function buildTaskAssignedMessage(Task $task, User $user): string
    {
        $message = "🔔 *New Task Assigned*\n\n";
        $message .= "*Task:* {$task->name}\n";

        if ($task->due_date) {
            $message .= "*Due:* {$task->due_date->format('M j, Y')}\n";
        }

        if ($task->booking) {
            $leadTraveler = $task->booking->leadTraveler();
            if ($leadTraveler) {
                $message .= "*Booking:* {$leadTraveler->last_name}, {$leadTraveler->first_name}\n";
            }
        }

        if ($task->description) {
            $message .= "\n*Details:*\n{$task->description}\n";
        }

        $message .= "\n📱 View in CRM: " . config('app.url');

        return $message;
    }

    /**
     * Test Twilio connection
     */
    public function testConnection(): array
    {
        if (!$this->client) {
            return [
                'success' => false,
                'error' => 'Twilio not configured - missing account SID or auth token'
            ];
        }

        try {
            // Try to fetch account info to test connection
            $account = $this->client->api->accounts($this->client->getAccountSid())->fetch();

            return [
                'success' => true,
                'account_sid' => $account->sid,
                'account_status' => $account->status,
                'from_number' => $this->fromNumber,
            ];
        } catch (RestException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }
}