<?php

namespace App\Jobs;

use App\Http\Helpers\KreativMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class SubscriptionReminderMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $as;
    public $be;
    public $bs;
    public $bex;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($be, $bex, $bs, $as)
    {
        $this->be = $be;
        $this->bex = $bex;
        $this->bs = $bs;
        $this->as = $as;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $be = $this->be;
        $bex = $this->bex;
        $bs = $this->bs;
        $as = $this->as;

        // Send Mail to Buyer
        $mailer = new KreativMailer;
        $data = [
            'toMail' => $as->email,
            'toName' => $as->name,
            'customer_name' => $as->name,
            'remaining_days' => $bex->expiration_reminder,
            'current_package_name' => $as->current_package->title,
            'expire_date' => Carbon::parse($as->expire_date)->toFormattedDateString(),
            'website_title' => $bs->website_title,
            'templateType' => 'subscription_expiry_reminder',
            'type' => 'subscriptionExpiryReminder'
        ];

        $mailer->mailFromAdmin($data);
    }
}
