<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\RentalOrder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RentalVehicleSession;
use App\Models\VehicleSessionLocation;

class KioskController extends Controller
{
    public function index()
    {




        $rentalOrders = $this->transformRentalOrder(RentalOrder::whereIn('status', ['paid', 'in payment'])->get());




        return Inertia::render('Kiosk/KioskIndex', compact(['rentalOrders']));
    }



    public function startsession(Request $request)
    {


        if (!User::where('pin', $request->pin)->exists()) {
            return back()->with(['error' => 'Invalid pin']);
        }


        $rentalOrder = RentalOrder::where('ref_number', $request->order_number)->first();
        $session = RentalVehicleSession::create([
            'rental_order_id' => $rentalOrder->id,
            'user_id' => User::where('pin', $request->pin)->first()->id,
            'session_token' => Str::random(40),
            'vehicle_id' => $rentalOrder->vehicle_id,
            'started_at' => now(),
            'status' => 'active',
            'employee_name' => User::where('pin', $request->pin)->first()->name,
            'total_cost' => $rentalOrder->total_amount,
        ]);


        $rentalOrder->update(['status' => 'in session']);

        $rentalOrder->vehicle->update(['status' => 'in use']);


        return redirect()->route('kiosk.session', [
            'sessionId' => $session->session_token,
            'rentalOrderId' => $rentalOrder->id,
        ]);
    }


    public function session($sessionToken, $rentalOrderId)
    {
        $session = RentalVehicleSession::with(['vehicle', 'rentalOrder.user', 'vehicleSessionLocations' => function ($query) {
            $query->latest()->first();
        }])->where('session_token', $sessionToken)
            ->where('rental_order_id', $rentalOrderId)
            ->where('status', '!=', 'completed')
            ->first();




        if (!$session) {
            return redirect()->route('kiosk.index')->with(['error' => 'Session not found or already completed']);
        }

        return Inertia::render('Kiosk/KioskSession', [
            'sessionId' => $sessionToken,
            'rentalOrderId' => $rentalOrderId,
            'session' => $this->transformSession($session)
        ]);
    }

    public function updateLocation(Request $request, $sessionToken)
    {
        $session = RentalVehicleSession::where('session_token', $sessionToken)->first();

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        $location = VehicleSessionLocation::create([
            'rental_vehicle_session_id' => $session->id,
            'vehicle_id' => $session->vehicle_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'recorded_at' => now(),
            'speed' => $request->speed ?? 0,
            'direction' => $request->direction ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'location' => $location
        ]);
    }


    public function closeSession($sessionToken)
    {
        $session = RentalVehicleSession::where('session_token', $sessionToken)->first();



        if (!$session) {
            return redirect()->route('kiosk.index')->with(['error' => 'Session not found']);
        }



        $session->update([
            'ended_at' => now(),
            'status' => 'completed',
        ]);

        $session->rentalOrder->update(['status' => 'completed']);

        $session->vehicle->update(['status' => 'Available']);

        return redirect()->route('kiosk.index')->with(['success' => 'Session closed successfully']);
    }

    private function transformRentalOrder($orders)
    {
        return  collect($orders)->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->ref_number,
                'customer_name' => $order->user->name,
                'vehicle' => $order->vehicle->name(),
                'plate_no' => $order->vehicle->plate_no,
                'start_date' => $order->rental_date,
                'end_date' => $order->return_date,
                'status' => $order->status,
            ];
        });
    }

    private function transformSession($session)
    {
        return [
            'id' => $session->id,
            'session_token' => $session->session_token,
            'status' => $session->status,
            'started_at' => $session->started_at,
            'vehicle' => [
                'model' => $session->vehicle->name(),
                'plate' => $session->vehicle->plate_no
            ],
            'customer' => [
                'name' => $session->rentalOrder->user->name,
                'phone' => $session->rentalOrder->user->phone ?? 'N/A'
            ],
            'latest_location' => $session->vehicleSessionLocations->first() ? [
                'lat' => $session->vehicleSessionLocations->first()->latitude,
                'lng' => $session->vehicleSessionLocations->first()->longitude,
                'recorded_at' => $session->vehicleSessionLocations->first()->recorded_at,
                'speed' => $session->vehicleSessionLocations->first()->speed,
            ] : null
        ];
    }
}
