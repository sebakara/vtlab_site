<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PackageOrder;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = PackageOrder::where('user_id',Auth::user()->id)->orderBy('id','DESC')->get();

        return view('user.package-order',compact('orders'));

    }

    public function orderdetails($id)
    {
        $data = PackageOrder::findOrFail($id);

        return view('user.package_order_details',compact('data'));

    }
}
