<?php

namespace App\Http\Controllers\Internal;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\RentalOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RentalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RentalOrder::with(['user.profile', 'vehicle']);

        if($request->status) {
            $query->where('status', $request->status);
        }

        $rentalOrders = $query->latest()
            ->paginate(10);

        return Inertia::render('Internal/RentalOrder/RentalOrderIndex', [
            'rentalOrders' => $rentalOrders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = User::role('customer')
            ->with('profile')
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile' => $user->profile
            ]);

        $vehicles = Vehicle::where('status', 'Available')
            ->get()
            ->map(fn($vehicle) => [
                'id' => $vehicle->id,
                'name' => "{$vehicle->brand} {$vehicle->model}",
                'plate_no' => $vehicle->plate_no,
                'rental_rate' => $vehicle->rental_rate
            ]);

        return Inertia::render('Internal/RentalOrder/RentalOrderCreate', [
            'customers' => $customers,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,active,completed,cancelled'
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Calculate total amount
        $start = new \DateTime($request->start_date);
        $end = new \DateTime($request->end_date);
        $days = $start->diff($end)->days + 1;
        $total_amount = $days * $vehicle->rental_rate;

        $rentalOrder = RentalOrder::create([
            'user_id' => $request->user_id,
            'vehicle_id' => $request->vehicle_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_amount' => $total_amount,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        // Update vehicle status
        $vehicle->update(['status' => 'Reserved']);

        return redirect()->route('internal.rental-orders.index')
            ->with('success', 'Rental order created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rentalOrder = RentalOrder::with(['user.profile', 'vehicle', 'attachments'])
            ->findOrFail($id);

        return Inertia::render('Internal/RentalOrder/RentalOrderShow', [
            'rentalOrder' => $rentalOrder
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rentalOrder = RentalOrder::with(['user.profile', 'vehicle'])->findOrFail($id);

        $customers = User::role('customer')
            ->with('profile')
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile' => $user->profile
            ]);

        $vehicles = Vehicle::whereIn('status', ['Available', $rentalOrder->vehicle->status])
            ->get()
            ->map(fn($vehicle) => [
                'id' => $vehicle->id,
                'name' => "{$vehicle->brand} {$vehicle->model}",
                'plate_no' => $vehicle->plate_no,
                'rental_rate' => $vehicle->rental_rate
            ]);

        return Inertia::render('Internal/RentalOrder/RentalOrderEdit', [
            'rentalOrder' => $rentalOrder,
            'customers' => $customers,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rentalOrder = RentalOrder::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,active,completed,cancelled'
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        // Calculate total amount
        $start = new \DateTime($request->start_date);
        $end = new \DateTime($request->end_date);
        $days = $start->diff($end)->days + 1;
        $total_amount = $days * $vehicle->rental_rate;

        // Update old vehicle status if changed
        if ($rentalOrder->vehicle_id !== $request->vehicle_id) {
            $rentalOrder->vehicle->update(['status' => 'Available']);
            $vehicle->update(['status' => 'Reserved']);
        }

        $rentalOrder->update([
            'user_id' => $request->user_id,
            'vehicle_id' => $request->vehicle_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_amount' => $total_amount,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('internal.rental-orders.index')
            ->with('success', 'Rental order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rentalOrder = RentalOrder::findOrFail($id);

        // Update vehicle status back to available
        $rentalOrder->vehicle->update(['status' => 'Available']);

        $rentalOrder->delete();

        return redirect()->route('internal.rental-orders.index')
            ->with('success', 'Rental order deleted successfully');
    }
}
