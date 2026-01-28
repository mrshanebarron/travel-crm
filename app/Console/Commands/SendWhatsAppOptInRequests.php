<?php

namespace App\Console\Commands;

use App\Services\TwilioService;
use Illuminate\Console\Command;

class SendWhatsAppOptInRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:send-opt-in {--user= : Send to specific user ID only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp opt-in requests to team members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending WhatsApp opt-in requests...');
        $this->newLine();

        $twilioService = app(TwilioService::class);

        if ($userId = $this->option('user')) {
            // Send to specific user
            $user = \App\Models\User::findOrFail($userId);
            $result = $twilioService->sendOptInRequest($user);

            if ($result['success']) {
                $this->info("✅ Opt-in request sent to {$user->name} ({$user->phone})");
            } else {
                $this->error("❌ Failed to send to {$user->name}: {$result['error']}");
            }
        } else {
            // Send to all eligible users
            $results = $twilioService->sendBulkOptInRequests();

            $this->info("📊 Results:");
            $this->info("Total users with phone numbers: {$results['total']}");
            $this->info("Successfully sent: {$results['sent']}");
            $this->info("Failed: {$results['failed']}");

            if (!empty($results['errors'])) {
                $this->newLine();
                $this->error("Errors:");
                foreach ($results['errors'] as $error) {
                    $this->error("- {$error}");
                }
            }
        }

        $this->newLine();
        $this->info('✅ Opt-in request process complete!');
        $this->info('Team members will need to reply "YES" to confirm they want to receive notifications.');

        return self::SUCCESS;
    }
}
