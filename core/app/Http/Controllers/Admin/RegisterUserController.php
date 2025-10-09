<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Session;

class RegisterUserController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->term;

        $users = User::when($term, function($query, $term) {
            $query->where('username', 'like', '%' . $term . '%')->orWhere('email', 'like', '%' . $term . '%');
        })->paginate(10);
        return view('admin.register_user.index',compact('users'));
    }

    public function view($id)
    {
        $user = User::findOrFail($id);
        $orders = $user->orders()->paginate(10);
        return view('admin.register_user.details',compact('user', 'orders'));

    }


    public function userban(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'status' => $request->status,
        ]);

        Session::flash('success', $user->username.' status update successfully!');
        return back();
    }


    public function emailStatus(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->update([
            'email_verified' => $request->email_verified,
        ]);

        Session::flash('success', 'Email status updated for ' . $user->username);
        return back();
    }

    public function delete(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if ($user->conversations()->count() > 0) {
            $convs = $user->conversations()->get();
            foreach ($convs as $key => $conv) {
                @unlink('assets/front/user-suppor-file/' . $conv->file);
                $conv->delete();
            }
        }

        if ($user->courseOrder()->count() > 0) {
            $coursePurchases = $user->courseOrder()->get();
            foreach ($coursePurchases as $key => $cp) {
                @unlink('assets/front/receipt/' . $cp->receipt);
                @unlink('assets/front/invoices/course/' . $cp->invoice);
                $cp->delete();
            }
        }

        if ($user->course_reviews()->count() > 0) {
            $user->course_reviews()->delete();
        }

        if ($user->donation_details()->count() > 0) {
            $donations = $user->donation_details()->get();
            foreach ($donations as $key => $donation) {
                @unlink('assets/front/receipt/' . $donation->receipt);
                $donation->delete();
            }
        }

        if ($user->event_details()->count() > 0) {
            $bookings = $user->event_details()->get();
            foreach ($bookings as $key => $booking) {
                @unlink('assets/front/receipt/' . $booking->receipt);
                @unlink('assets/front/invoices/' . $booking->invoice);
                $booking->delete();
            }
        }

        if ($user->order_items()->count() > 0) {
            $user->order_items()->delete();
        }

        if ($user->package_orders()->count() > 0) {
            $pos = $user->package_orders()->get();
            foreach ($pos as $key => $po) {
                @unlink('assets/front/receipt/' . $po->receipt);
                @unlink('assets/front/invoices/' . $po->invoice);
                $po->delete();
            }
        }

        if ($user->orders()->count() > 0) {
            $orders = $user->orders()->get();
            foreach ($orders as $key => $order) {
                @unlink('assets/front/receipt/' . $order->receipt);
                @unlink('assets/front/invoices/product/' . $order->invoice_number);
                $order->delete();
            }
        }

        if ($user->product_reviews()->count() > 0) {
            $user->product_reviews()->delete();
        }

        if ($user->subscription()->count() > 0) {
            @unlink('assets/front/receipt/' . $user->subscription->receipt);
            @unlink('assets/front/invoices/' . $user->subscription->invoice);
            $user->subscription()->delete();
        }

        if ($user->tickets()->count() > 0) {
            $tickets = $user->tickets()->get();
            foreach ($tickets as $key => $ticket) {
                @unlink('assets/front/user-suppor-file/' . $ticket->zip_file);
                $ticket->delete();
            }
        }

        @unlink('assets/front/img/user/' . $user->photo);
        $user->delete();

        Session::flash('success', 'User deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $user = User::findOrFail($id);

            if ($user->conversations()->count() > 0) {
                $convs = $user->conversations()->get();
                foreach ($convs as $key => $conv) {
                    @unlink('assets/front/user-suppor-file/' . $conv->file);
                    $conv->delete();
                }
            }

            if ($user->courseOrder()->count() > 0) {
                $coursePurchases = $user->courseOrder()->get();
                foreach ($coursePurchases as $key => $cp) {
                    @unlink('assets/front/receipt/' . $cp->receipt);
                    @unlink('assets/front/invoices/course/' . $cp->invoice);
                    $cp->delete();
                }
            }

            if ($user->course_reviews()->count() > 0) {
                $user->course_reviews()->delete();
            }

            if ($user->donation_details()->count() > 0) {
                $donations = $user->donation_details()->get();
                foreach ($donations as $key => $donation) {
                    @unlink('assets/front/receipt/' . $donation->receipt);
                    $donation->delete();
                }
            }

            if ($user->event_details()->count() > 0) {
                $bookings = $user->event_details()->get();
                foreach ($bookings as $key => $booking) {
                    @unlink('assets/front/receipt/' . $booking->receipt);
                    @unlink('assets/front/invoices/' . $booking->invoice);
                    $booking->delete();
                }
            }

            if ($user->order_items()->count() > 0) {
                $user->order_items()->delete();
            }

            if ($user->package_orders()->count() > 0) {
                $pos = $user->package_orders()->get();
                foreach ($pos as $key => $po) {
                    @unlink('assets/front/receipt/' . $po->receipt);
                    @unlink('assets/front/invoices/' . $po->invoice);
                    $po->delete();
                }
            }

            if ($user->orders()->count() > 0) {
                $orders = $user->orders()->get();
                foreach ($orders as $key => $order) {
                    @unlink('assets/front/receipt/' . $order->receipt);
                    @unlink('assets/front/invoices/product/' . $order->invoice_number);
                    $order->delete();
                }
            }

            if ($user->product_reviews()->count() > 0) {
                $user->product_reviews()->delete();
            }

            if ($user->subscription()->count() > 0) {
                @unlink('assets/front/receipt/' . $user->subscription->receipt);
                @unlink('assets/front/invoices/' . $user->subscription->invoice);
                $user->subscription()->delete();
            }

            if ($user->tickets()->count() > 0) {
                $tickets = $user->tickets()->get();
                foreach ($tickets as $key => $ticket) {
                    @unlink('assets/front/user-suppor-file/' . $ticket->zip_file);
                    $ticket->delete();
                }
            }

            @unlink('assets/front/img/user/' . $user->photo);
            $user->delete();
        }

        Session::flash('success', 'Users deleted successfully!');
        return "success";
    }


    public function changePass($id) {
        $data['user'] = User::findOrFail($id);
        return view('admin.register_user.password', $data);
    }


    public function updatePassword(Request $request)
    {

        $messages = [
            'cpass.required' => 'Current password is required',
            'npass.required' => 'New password is required',
            'cfpass.required' => 'Confirm password is required',
        ];

        $request->validate([
            'cpass' => 'required',
            'npass' => 'required',
            'cfpass' => 'required',
        ], $messages);


        $user = User::findOrFail($request->user_id);
        if ($request->cpass) {
            if (Hash::check($request->cpass, $user->password)) {
                if ($request->npass == $request->cfpass) {
                    $input['password'] = Hash::make($request->npass);
                } else {
                    return back()->with('error', __('Confirm password does not match.'));
                }
            } else {
                return back()->with('error', __('Current password Does not match.'));
            }
        }

        $user->update($input);

        Session::flash('success', 'Password update for ' . $user->username);
        return back();
    }
}
