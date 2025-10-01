<?php

namespace App\Http\Controllers\Costumer;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RentalController extends Controller
{
    public function index()
    {
        return inertia::render('Costumer/RentalPage');
    }
}
