<?php

use App\Models\Vehicle;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintainance_vehicle_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ref_number')->unique();
            $table->foreignIdFor(Vehicle::class)->constrained()->onDelete('cascade');
            $table->longText('description')->nullable();
            $table->date('requested_date')->nullable();
            $table->date('maintainance_date')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->float('cost')->nullable();
            $table->string('requested_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintainance_vehicle_requests');
    }
};
