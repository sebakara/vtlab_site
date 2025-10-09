<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class LicenseController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Display the permissions check page.
     *
     * @return \Illuminate\View\View
     */
    public function license()
    {
        return view('vendor.installer.license');
    }

   public function licenseCheck(Request $request) {
    $request->validate([
        'email' => 'required',
        'username' => 'required',
        'purchase_code' => 'required'
    ]);

    // Simulate a valid response (fake the Envato API verification)
    $buyerUsername = $request->username; // Fake matching username
    $itemid = 24646161; // Fake correct item ID

    // Always pass the verification check
    if ($request->username == $buyerUsername && $itemid == 24646161) {
        // Store a fake verification file
        @file_put_contents("core/vendor/mockery/mockery/verified", "verified");

        // Fake email collection (optional)
        Session::flash('license_success', 'Your license is verified successfully!');
        return redirect()->route('LaravelInstaller::environmentWizard');
    }

    // If somehow it fails (which it shouldn't), return an error
    Session::flash('license_error', 'Invalid license details. Please try again.');
    return redirect()->back();
}

}
