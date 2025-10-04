<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $userRole = User::find(Auth::user()->id)->roles()->first();

        if ($userRole->name == 'customer') {
            return redirect()->route('customer.dashboard');
        }


        return redirect()->route('internal.dashboard');
    }
}
