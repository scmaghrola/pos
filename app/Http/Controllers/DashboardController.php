<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class DashboardController extends Controller
{
    function index(){
        $customers=Customer::count();
        return view('pos.dashboard',compact('customers')) ;
    }
}
