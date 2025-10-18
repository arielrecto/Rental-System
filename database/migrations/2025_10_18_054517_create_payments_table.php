<?php

use App\Models\User;
use App\Models\PaymentAccount;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('memo')->nullable();
            $table->string('ref_number')->nullable()->unique();
            $table->string('total_amount');
            $table->morphs('payable');
            $table->string('status')->default('pending');
            $table->foreignIdFor(User::class, 'paid_by')->constraind('users')->cascadeOnDelete();
            $table->foreignIdFor(PaymentAccount::class)->constraind()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
