<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request){
        $paymentMethods = array_keys(config('payments.gateways'));
        return view('checkout.index', compact('paymentMethods'));
    }
}
