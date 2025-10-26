<?php

use App\Models\RentalOrder;
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
        Schema::create('rental_vehicle_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(RentalOrder::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Vehicle::class)->constrained()->onDelete('cascade');
            $table->string('session_token')->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('employee_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_vehicle_sessions');
    }
};
