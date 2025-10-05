<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function revenue (){
        return Inertia::render('Internal/Report/RevenueReport');
    }


    public function rentalAnalytic(){
        return Inertia::render('Internal/Report/RentalAnalytics');
    }

    public function vehicle(){
        return Inertia::render('Internal/Report/VehicleReport');
    }
}
