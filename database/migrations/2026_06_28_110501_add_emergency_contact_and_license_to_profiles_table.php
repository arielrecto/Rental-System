<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('address')->nullable()->change();

            $table->string('emergency_contact_name')->nullable()->after('birth_date');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_relationship');

            $table->string('drivers_license_number')->nullable()->after('emergency_contact_phone');
            $table->date('drivers_license_expiry')->nullable()->after('drivers_license_number');
            $table->boolean('drivers_license_verified')->default(false)->after('drivers_license_expiry');
            $table->timestamp('drivers_license_verified_at')->nullable()->after('drivers_license_verified');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'drivers_license_number',
                'drivers_license_expiry',
                'drivers_license_verified',
                'drivers_license_verified_at',
            ]);
        });
    }
};
