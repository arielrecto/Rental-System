<?php

use App\Models\Depreciation;
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
        Schema::create('depreciation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Depreciation::class);
            $table->string('name'); 
            $table->date('depreciation_date');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depreciation_items');
    }
};
