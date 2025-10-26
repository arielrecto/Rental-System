<?php

namespace Database\Seeders;

use App\Models\PaymentAccount;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentAccounts = [
            [
                'account_number' => 11233,
                'account_name' => "ariel-123",
                'provider' => "gcash",
                'descriptions' => null,
                'is_active' => true,
            ]
        ];


        collect($paymentAccounts)->map(function($account){
            PaymentAccount::create([
                ...$account
            ]);
        });
    }
}
