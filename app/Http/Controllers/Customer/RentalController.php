<?php

namespace App\Http\Controllers\Customer;

use App\Actions\GenerateSequence;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Vehicle;
use App\Models\Attachment;
use App\Models\RentalOrder;
use Illuminate\Http\Request;
use function Pest\Laravel\put;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RentalController extends Controller
{


    public function __contruct( GenerateSequence $generateSequence)
    {

    }
    public function index()
    {


        $vehicles = Vehicle::with(['image'])->where('status', 'Available')->paginate(12);

        return inertia::render('Customer/Rentals/RentalPage', compact([
            'vehicles'
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string|max:500',
            'attachments.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);


        $totalDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)->addDay(1));


        // Create rental order
        $rentalOrder = RentalOrder::create([
            'user_id' => auth()->id(),
            'vehicle_id' => $request->vehicle_id,
            'rental_date' => $request->start_date,
            'return_date' => $request->end_date,
            'total_amount' => $vehicle->rental_rate *  $totalDays,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        $rentalOrder->update([
            'ref_number' => GenerateSequence::generateRefNumber('RENT', 6, $rentalOrder->id)
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {

                $fileName = 'RO-' . uniqid() . $file->getClientOriginalExtension();
                $path = $file->storeAs('rental-attachments', $fileName, 'public');

                $attachment = new Attachment([
                    'file_name' => $fileName,
                    'file_path' => asset('/storage/' . $path),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'file_extension' => $file->getClientOriginalExtension()
                ]);

                $rentalOrder->attachments()->save($attachment);
            }
        }

        // Update vehicle status
        $vehicle->update(['status' => 'Reserved']);

        return redirect()->back()->with('success', 'Rental order created successfully!');
    }


    public function history()
    {
        $rentalOrders = RentalOrder::with(['vehicle', 'attachments'])->where('user_id', Auth::user()->id)->latest()->paginate(10);

        return Inertia::render('Customer/Rentals/RentalHistory', compact([
            'rentalOrders'
        ]));
    }


    public function cancel($id)
    {
        $rentalOrder = RentalOrder::findOrFail($id);


        $rentalOrder->update(['status' => 'cancel']);


        return back()->with(['success' => 'Rent Order Canceled']);
    }
}
