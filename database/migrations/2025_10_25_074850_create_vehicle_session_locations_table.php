<?php

use App\Models\RentalVehicleSession;
use App\Models\Vehicle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_session_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RentalVehicleSession::class)->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('recorded_at')->useCurrent();
            $table->string('location_description')->nullable();
            $table->string('speed')->nullable();
            $table->string('direction')->nullable();
            $table->foreignIdFor(Vehicle::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_session_locations');
    }
};
