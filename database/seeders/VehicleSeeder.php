<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'brand'    => 'Yamaha Aerox V2 155',
                'model'    => 'Aerox V2',
                'color'    => 'White',
                'year'     => '2022',
                'plate_no' => 'D914GV',
                'manufacturer' => 'Yamaha',
                'status'   => 'Available',
            ],
            [
                'brand'    => 'Yamaha Fazzio 125',
                'model'    => 'Fazzio',
                'color'    => 'Black',
                'year'     => '2022',
                'plate_no' => '0416BF',
                'manufacturer' => 'Yamaha',
                'status'   => 'Available',
            ],
            [
                'brand'    => 'Honda ADV 160',
                'model'    => 'ADV 160',
                'color'    => 'White',
                'year'     => '2022',
                'plate_no' => 'D175GS',
                'manufacturer' => 'Honda',
                'status'   => 'Available',
            ],
            [
                'brand'    => 'Honda Airblade 160',
                'model'    => 'Airblade 160',
                'color'    => 'Red',
                'year'     => '2023',
                'plate_no' => 'D422BG',
                'manufacturer' => 'Honda',
                'status'   => 'Available',
            ],
            [
                'brand'    => 'Honda Beat V3 125',
                'model'    => 'Beat V3',
                'color'    => 'Black',
                'year'     => '2023',
                'plate_no' => 'D797EO',
                'manufacturer' => 'Honda',
                'status'   => 'Available',
            ],
            [
                'brand'    => 'Honda Click V2 125',
                'model'    => 'Click V2',
                'color'    => 'Grey',
                'year'     => '2019',
                'plate_no' => '0948BN',
                'manufacturer' => 'Honda',
                'status'   => 'Available',
            ],
            [
                'brand'    => 'Yamaha Nmax V2 155',
                'model'    => 'Nmax V2',
                'color'    => 'Grey',
                'year'     => '2021',
                'plate_no' => '0663CC',
                'manufacturer' => 'Yamaha',
                'status'   => 'Available',
            ],
            [
                'brand'    => 'Honda PXC V2 160',
                'model'    => 'PXC V2',
                'color'    => 'Red',
                'year'     => '2022',
                'plate_no' => 'D343UH',
                'manufacturer' => 'Honda',
                'status'   => 'Available',
            ],
            [
                'brand'    => 'Yamaha Sniper 155',
                'model'    => 'Sniper',
                'color'    => 'Brown',
                'year'     => '2020',
                'plate_no' => '6740FW',
                'manufacturer' => 'Yamaha',
                'status'   => 'Available',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
