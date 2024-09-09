<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderEmail;
use App\Models\Domainrenewal;
use Carbon\Carbon;
class SendEmailBeforeDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-before-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email before a specified date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expirationMonth = now()->addMonth()->format('m');

$domains = Domainrenewal::whereMonth('domain_expire_date', '=', $expirationMonth)->get();

      
        foreach ($domains as $domain) {
            $this->info('Sending email for domain: ' . $domain->sitename);
            try {
            Mail::to('sakthiganapathis97@gmail.com')->send(new ReminderEmail($domain));
            $this->info('Email sent for domain: ' . $domain->sitename);
            } catch (\Exception $e) {
                $this->error('Error sending email for domain: ' . $domain->sitename . ': ' . $e->getMessage());
            }
        }

        if ($domains->isEmpty()) {
            $this->info('No domains expiring one month from now.');
        }
    }
}
