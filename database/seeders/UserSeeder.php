<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $divisionIds = Division::pluck('id')->toArray();
        $divisionIdsJson = json_encode($divisionIds);
        User::create(
            [
                'first_name' => 'Super',
                'middle_name' => '',
                'last_name' => 'Admin',
                'user_name' => 'S.Admin',
                'province' => 'Albay',
                'position' => 'Developer',
                'mobile_number' => '09123456789',
                'email' => 'opcr.dole@gmail.com',
                'role_id' => '1',
                'division_id' => '["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"]',
                'status' => 'Active',
                'expiration_date' => Carbon::now()->addDays(90),
                'password' => Hash::make('admin000..'),
                'created_by' => 'A.bong',
            ]
        );
       
    }
}
