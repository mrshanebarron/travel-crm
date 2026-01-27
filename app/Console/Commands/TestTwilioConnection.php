<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestTwilioConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Twilio WhatsApp connection and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Twilio WhatsApp Integration...');
        $this->newLine();

        $twilioService = app(\App\Services\TwilioService::class);
        $result = $twilioService->testConnection();

        if ($result['success']) {
            $this->info('✅ Twilio connection successful!');
            $this->info("Account SID: {$result['account_sid']}");
            $this->info("Account Status: {$result['account_status']}");
            $this->info("WhatsApp From Number: {$result['from_number']}");
        } else {
            $this->error('❌ Twilio connection failed:');
            $this->error($result['error']);

            if (isset($result['code'])) {
                $this->error("Error Code: {$result['code']}");
            }

            $this->newLine();
            $this->warn('Make sure you have set the following environment variables:');
            $this->warn('TWILIO_ACCOUNT_SID=your_account_sid');
            $this->warn('TWILIO_AUTH_TOKEN=your_auth_token');
            $this->warn('TWILIO_WHATSAPP_FROM=+14155238886');
        }

        return $result['success'] ? self::SUCCESS : self::FAILURE;
    }
}
