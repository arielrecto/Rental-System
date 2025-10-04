<?php

namespace App\Http\Controllers\Internal;

use Inertia\Inertia;
use App\Models\Vehicle;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['image'])->latest()->paginate(12);


        return Inertia::render('Internal/Vehicle/VehicleIndex', compact(['vehicles']));
    }

    public function create()
    {
        return Inertia::render('Internal/Vehicle/VehicleCreate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'plate_no' => 'required|string|max:255|unique:vehicles',
            'type' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|numeric|min:0',
            'rental_rate' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $vehicle = Vehicle::create($request->except('image'));

        if ($request->hasFile('image')) {
            $fileName = 'VP-' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('vehicles', $fileName, 'public');

            Attachment::create([
                'file_name' => $request->file('image')->getClientOriginalName(),
                'file_path' => asset('/storage/' . $path),
                'file_type' => $request->file('image')->getMimeType(),
                'file_size' => $request->file('image')->getSize(),
                'file_extension' => $request->file('image')->getClientOriginalExtension(),
                'attachable_id' => $vehicle->id,
                'attachable_type' => get_class($vehicle)
            ]);
        }


        return redirect()->route('internal.vehicles.index')
            ->with('success', 'Vehicle created successfully.');
    }

    public function edit(string $id)
    {
        $vehicle = Vehicle::with('asset')->findOrFail($id);
        return Inertia::render('Internal/Vehicle/VehicleEdit', compact('vehicle'));
    }

    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'plate_no' => 'required|string|max:255|unique:vehicles,plate_no,' . $id,
            'type' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'required|numeric|min:0',
            'rental_rate' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        $vehicle->update($request->except('image'));

        if ($request->hasFile('image')) {
            $fileName = 'VP-' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $path = $request->file('image')->storeAs('vehicles', $fileName, 'public');

            Attachment::create([
                'file_name' => $request->file('image')->getClientOriginalName(),
                'file_path' => asset('/storage/' . $path),
                'file_type' => $request->file('image')->getMimeType(),
                'file_size' => $request->file('image')->getSize(),
                'file_extension' => $request->file('image')->getClientOriginalExtension(),
                'attachable_id' => $vehicle->id,
                'attachable_type' => get_class($vehicle)
            ]);
        }

        return redirect()->route('internal.vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // Delete associated image
        if ($vehicle->asset) {
            Storage::disk('public')->delete($vehicle->asset->file_path);
            $vehicle->asset->delete();
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }
}
