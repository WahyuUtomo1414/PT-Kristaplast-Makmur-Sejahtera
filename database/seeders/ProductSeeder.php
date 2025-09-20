<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert product types
        $types = [
            'Botol',
            'Gelas',
            'Kantong Plastik',
            'Ember',
            'Container',
        ];

        foreach ($types as $type) {
            DB::table('product_type')->insert([
                'name' => $type,
                'status_id' => 1
            ]);
        }

        // Ambil type_id
        $typeIds = DB::table('product_type')->pluck('id')->toArray();

        // Insert 20 produk plastik
        $products = [
            'Botol Minum 600ml',
            'Botol Minum 1 Liter',
            'Gelas Plastik 12oz',
            'Gelas Plastik 16oz',
            'Kantong Plastik Hitam Besar',
            'Kantong Plastik Putih Sedang',
            'Ember Plastik 20 Liter',
            'Ember Plastik 10 Liter',
            'Container Makanan Kecil',
            'Container Makanan Sedang',
            'Container Makanan Besar',
            'Tupperware Mini',
            'Toples Plastik Kecil',
            'Toples Plastik Besar',
            'Piring Plastik Bulat',
            'Piring Plastik Kotak',
            'Sendok Plastik',
            'Garpu Plastik',
            'Jerigen Plastik 5 Liter',
            'Jerigen Plastik 10 Liter',
        ];

        foreach ($products as $name) {
            DB::table('product')->insert([
                'product_type_id' => $typeIds[array_rand($typeIds)],
                'name' => $name,
                'images' => 'default.png',
                'desc' => 'Produk ' . strtolower($name) . ' berkualitas untuk kebutuhan sehari-hari.',
                'price' => rand(2000, 50000),
                'status_id' => 1, // default ready
            ]);
        }
    }
}
