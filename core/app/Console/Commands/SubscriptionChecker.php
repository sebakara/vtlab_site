<?php

namespace App\Console\Commands;

use App\BasicExtended;
use App\BasicExtra;
use App\BasicSetting;
use App\Jobs\SubscriptionExpiredMail;
use App\Jobs\SubscriptionReminderMail;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SubscriptionChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for subscription expire date, activate next package (if any) or expire the subscription';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $activeSubs = Subscription::where('status', 1)->get();
        $be = BasicExtended::first();
        $bex = BasicExtra::first();
        $bs = BasicSetting::first();

        foreach ($activeSubs as $key => $as) {
            // if the current package is expired
            if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($as->expire_date))) {
                // if the subscription has a next package
                if (!empty($as->next_package_id)) {
                    // current package will be next package
                    $as->current_package_id = $as->next_package_id;
                    $as->next_package_id = NULL;
                    $as->current_payment_method = $as->next_payment_method;
                    $as->next_payment_method = NULL;
                    $as->status = 1;
                    $as->save();

                    // calc new expire date
                    $currPackage = $as->current_package;
                    if ($currPackage->duration == 'monthly') {
                        $days = 30;
                    } else {
                        $days = 365;
                    }
                    $newExpireDate = Carbon::now()->addDays($days);

                    $as->expire_date = $newExpireDate;
                    $as->save();
                }
                // if the subscription has no next package
                else {
                    $edate = $as->expire_date;
                    $as->expire_date = NULL;
                    $as->status = 0; // expired
                    $as->save();

                    SubscriptionExpiredMail::dispatch($bs, $as->email, $as->name, $as->current_package, $edate)->delay(now()->addMinutes(5));
                }
            }
            // if the current package is not expired yet
            else {
                // If it has no next package
                if (empty($as->next_package_id)) {
                    // if difference between 'current package expire date & today' is equal to 'expiration_reminder'
                    if (Carbon::parse($as->expire_date)->diffInDays(Carbon::now()) == $bex->expiration_reminder) {
                        // then, send a mail notification with 'current package expire date' & 'remaining days'
                        SubscriptionReminderMail::dispatch($be, $bex, $bs, $as)->delay(now()->addMinutes(5));
                    }
                }
            }
        }

        \Artisan::call("queue:work", ["--stop-when-empty"]);

    }
}
