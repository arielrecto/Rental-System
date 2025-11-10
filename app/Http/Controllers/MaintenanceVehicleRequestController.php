<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\MaintainanceVehicleRequest;
use Illuminate\Support\Str;

class MaintenanceVehicleRequestController extends Controller
{
    public function index()
    {
        $requests = MaintainanceVehicleRequest::with(['vehicle'])
            ->latest()
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'ref_number' => $request->ref_number,
                    'vehicle_name' => $request->vehicle->brand . ' ' . $request->vehicle->model,
                    'vehicle_plate' => $request->vehicle->plate_no,
                    'description' => $request->description,
                    'requested_date' => $request->requested_date,
                    'maintainance_date' => $request->maintainance_date,
                    'is_completed' => $request->is_completed,
                    'cost' => $request->cost,
                    'status' => $request->is_completed ? 'Completed' : 'Pending'
                ];
            });




        return Inertia::render('Internal/Maintenance/MaintenanceIndex', [
            'requests' => $requests
        ]);
    }

    public function create()
    {
        $vehicles = Vehicle::select('id', 'brand', 'model', 'plate_no')->get();

        return Inertia::render('Internal/Maintenance/MaintenanceCreate', [
            'vehicles' => $vehicles
        ]);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'description' => 'required|string',
            'requested_date' => 'required|date',
            'maintainance_date' => 'required|date|after_or_equal:requested_date',
            'cost' => 'required|numeric|min:0'
        ]);

        $vehicle = Vehicle::find($request->vehicle_id);


        $validated['ref_number'] = 'MR-' . Str::random(8);
        $validated['requested_by'] = auth()->id();
        $validated['is_completed'] = false;

        MaintainanceVehicleRequest::create($validated);



        $vehicle->update([
            'status' => 'maintenance'
        ]);

        return redirect()->route('internal.maintenance.index')
            ->with('success', 'Maintenance request created successfully.');
    }

    public function show($id)
    {
        $request = MaintainanceVehicleRequest::with('vehicle')
            ->findOrFail($id);

        return Inertia::render('Internal/Maintenance/MaintenanceShow', [
            'request' => [
                'id' => $request->id,
                'ref_number' => $request->ref_number,
                'vehicle' => [
                    'name' => $request->vehicle->brand . ' ' . $request->vehicle->model,
                    'plate' => $request->vehicle->plate_no
                ],
                'description' => $request->description,
                'requested_date' => $request->requested_date,
                'maintainance_date' => $request->maintainance_date,
                'is_completed' => $request->is_completed,
                'cost' => $request->cost,
                'status' => $request->is_completed ? 'Completed' : 'Pending'
            ]
        ]);
    }

    public function edit($id)
    {
        $request = MaintainanceVehicleRequest::with('vehicle')->findOrFail($id);
        $vehicles = Vehicle::select('id', 'brand', 'model', 'plate_no')->get();

        return Inertia::render('Internal/Maintenance/MaintenanceEdit', [
            'request' => $request,
            'vehicles' => $vehicles
        ]);
    }

    public function update(Request $request, $id)
    {
        $maintenance = MaintainanceVehicleRequest::findOrFail($id);

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'description' => 'required|string',
            'requested_date' => 'required|date',
            'maintainance_date' => 'required|date|after_or_equal:requested_date',
            'cost' => 'required|numeric|min:0',
            'is_completed' => 'boolean'
        ]);

        $maintenance->update($validated);

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance request updated successfully.');
    }

    public function destroy($id)
    {
        $maintenance = MaintainanceVehicleRequest::findOrFail($id);
        $maintenance->delete();

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance request deleted successfully.');
    }

    public function markAsCompleted($id)
    {
        $maintenance = MaintainanceVehicleRequest::findOrFail($id);
        $maintenance->is_completed = true;
        $maintenance->save();

        $vehicle = Vehicle::find($maintenance->vehicle_id);
        $vehicle->status = 'available';
        $vehicle->save();

        return redirect()->route('internal.maintenance.index')
            ->with('success', 'Maintenance request marked as completed.');
    }

    public function markAsOnGoing($id)
    {
        $maintenance = MaintainanceVehicleRequest::findOrFail($id);
        $maintenance->is_completed = true;
        $maintenance->save();

        $vehicle = Vehicle::find($maintenance->vehicle_id);
        $vehicle->status = 'maintenance';
        $vehicle->save();

        return redirect()->route('internal.maintenance.index')
            ->with('success', 'Maintenance request marked as On Going.');
    }
}
