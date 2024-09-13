<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->delete();

        DB::table('categories')->insert([
            [
                'title' => 'Mobiles',
                'details' => 'All kinds of mobile phones and accessories',
            ],
            [
                'title' => 'Laptops',
                'details' => 'Various brands and types of laptops and accessories',
            ],
            [
                'title' => 'Clothing',
                'details' => 'Apparel, accessories, and fashion wear',
            ],
            [
                'title' => 'Books',
                'details' => 'Novels, educational materials, and more',
            ],
        ]);
    }
}
