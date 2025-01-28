<?php

namespace Database\Seeders;

use App\Models\CategoryUpload;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UploadCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Kasambahay Quarterly Reports',
            'JobStart Monthly Reports',
            'SPES Quarterly Reports',
            'Physical and Financial Targets',
            'Statistical Performance Reporting System',
            'PESO Reports',
            'BLR Monitoring Report',
            'DILP Livelihood Report',
            'CLPEP Quarterly Report',
            'GAD Activities Reporting Template',
        ];

        foreach ($categories as $category) {
            CategoryUpload::create(['category_name' => $category]);
        }
    }
}
