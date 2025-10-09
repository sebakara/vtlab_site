<?php

namespace App\Jobs;

use App\Http\Helpers\KreativMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class SubscriptionExpiredMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bs;
    public $email;
    public $name;
    public $package;
    public $edate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bs, $email, $name, $package, $edate)
    {
        $this->bs = $bs;
        $this->email = $email;
        $this->name = $name;
        $this->edate = $edate;
        $this->package = $package->title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = $this->email;
        $name = $this->name;
        $package = $this->package;
        $edate = $this->edate;
        $bs = $this->bs;

        // Send Mail to Buyer
        $mailer = new KreativMailer;
        $data = [
            'toMail' => $email,
            'toName' => $name,
            'customer_name' => $name,
            'packages_link' => route('front.packages'),
            'expired_package' => $package,
            'expire_date' => Carbon::parse($edate)->toFormattedDateString(),
            'website_title' => $bs->website_title,
            'templateType' => 'subscription_expired',
            'type' => 'subscriptionExpired'
        ];

        $mailer->mailFromAdmin($data);

    }
}
