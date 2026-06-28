<?php

namespace App\Http\Controllers\Customer;

use App\Actions\GenerateSequence;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Vehicle;
use App\Models\Attachment;
use App\Models\RentalOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['image'])->where('status', 'Available')->paginate(12);

        $userProfile = Auth::check()
            ? Auth::user()->load('profile.attachments')->profile
            : null;

        return Inertia::render('Customer/Rentals/RentalPage', compact('vehicles', 'userProfile'));
    }

    public function store(Request $request)
    {
        $user = Auth::user()->load('profile.attachments');
        $profileHasDocs = $user->profile?->attachments->isNotEmpty() ?? false;

        $request->validate([
            'vehicle_id'    => 'required|exists:vehicles,id',
            'start_date'    => 'required|date|after_or_equal:now',
            'end_date'      => 'required|date|after:start_date',
            'notes'         => 'nullable|string|max:500',
            'attachments.*' => $profileHasDocs
                ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240'
                : 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $totalDays = max(1, (int) ceil(
            Carbon::parse($request->start_date)->diffInMinutes(Carbon::parse($request->end_date)) / 1440
        ));

        $rentalOrder = RentalOrder::create([
            'user_id'      => $user->id,
            'vehicle_id'   => $request->vehicle_id,
            'rental_date'  => $request->start_date,
            'return_date'  => $request->end_date,
            'total_amount' => $vehicle->rental_rate * $totalDays,
            'status'       => 'pending',
            'notes'        => $request->notes,
        ]);

        $rentalOrder->update([
            'ref_number' => GenerateSequence::generateRefNumber('RENT', 6, $rentalOrder->id),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = 'RO-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('rental-attachments', $fileName, 'public');

                $rentalOrder->attachments()->save(new Attachment([
                    'file_name'      => $fileName,
                    'file_path'      => asset('/storage/' . $path),
                    'file_type'      => $file->getMimeType(),
                    'file_size'      => $file->getSize(),
                    'file_extension' => $file->getClientOriginalExtension(),
                    'category'       => 'rental_upload',
                ]));
            }
        } elseif ($profileHasDocs) {
            foreach ($user->profile->attachments as $profileAttachment) {
                $rentalOrder->attachments()->save(new Attachment([
                    'file_name'      => $profileAttachment->file_name,
                    'file_path'      => $profileAttachment->file_path,
                    'file_type'      => $profileAttachment->file_type,
                    'file_size'      => $profileAttachment->file_size,
                    'file_extension' => $profileAttachment->file_extension,
                    'category'       => $profileAttachment->category,
                ]));
            }
        }

        $vehicle->update(['status' => 'Reserved']);

        return redirect()->back()->with('success', 'Rental order created successfully!');
    }

    public function history()
    {
        $rentalOrders = RentalOrder::with(['vehicle', 'attachments'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return Inertia::render('Customer/Rentals/RentalHistory', compact('rentalOrders'));
    }

    public function cancel($id)
    {
        $rentalOrder = RentalOrder::findOrFail($id);
        $rentalOrder->update(['status' => 'cancel']);

        return back()->with(['success' => 'Rent Order Canceled']);
    }
}
