<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(
            [
                CompanySeeder::class,
                LineSeeder::class,
                EquipmentRoleSeeder::class,
                EquipmentModeSeeder::class,
                EquipmentTypeSeeder::class,
                MediaTypeSeeder::class,
                UnitSeeder::class,
                ConfigTypeSeeder::class,
                PassParametersSeeder::class,
                UserRoleSeeder::class,
                StationSeeder::class,
                EmvTypeSeeder::class,
                AcqParamSeeder::class,
                CardFeeSeeder::class,
                /*TidSeeder::class,*/
                EquipmentLocationEnd::class,
                UserSeeder::class,
                PaymentSchemeSeeder::class,
                BlacklistReasonSeeder::class,
                ]
        );
    }
}
