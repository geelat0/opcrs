<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Division::create(
            [
                'division_name'=> 'IMSD',
                'created_by'=> 'SuperAdmin',

            ]
          );

          Division::create(
            [
                'division_name'=> 'TSSD',
                'created_by'=> 'SuperAdmin',

            ]
          );
          Division::create(
            [
                'division_name'=> 'Albay PO',
                'created_by'=> 'SuperAdmin',

            ]
          );
          Division::create(
            [
                'division_name'=> 'Camarines_Sur PO',
                'created_by'=> 'SuperAdmin',

            ]
          );
          Division::create(
            [
                'division_name'=> 'Camarines_Norte PO',
                'created_by'=> 'SuperAdmin',

            ]
          );
          Division::create(
            [
                'division_name'=> 'Catanduanes PO',
                'created_by'=> 'SuperAdmin',

            ]
          );
          Division::create(
            [
                'division_name'=> 'Masbate PO',
                'created_by'=> 'SuperAdmin',

            ]
          );
          Division::create(
            [
                'division_name'=> 'Sorsogon PO',
                'created_by'=> 'SuperAdmin',

            ]
          );
          Division::create(
            [
                'division_name'=> 'RTWPB',
                'created_by'=> 'SuperAdmin',

            ]
          );
          Division::create(
            [
                'division_name'=> 'MALSU',
                'created_by'=> 'SuperAdmin',

            ]
          );
    }
}
