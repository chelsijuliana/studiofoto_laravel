<?php

namespace Database\Seeders;

use App\Models\OperatingHour;
use Illuminate\Database\Seeder;

class OperatingHourSeeder extends Seeder
{
    public function run(): void
    {
        // hari: 0 = Minggu, 1 = Senin, ... 6 = Sabtu
        for ($hari = 0; $hari <= 6; $hari++) {
            OperatingHour::create([
                'hari' => $hari,
                'jam_buka' => $hari === 0 ? null : '10:00',
                'jam_tutup' => $hari === 0 ? null : '20:00',
                'is_tutup' => $hari === 0,
            ]);
        }
    }
}