<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestoSeeder extends Seeder
{
    public function run(): void
    {
        // Masukkan Menu
        DB::table('menus')->insert([
            ['name' => 'Nasi Goreng Spesial', 'price' => 20000, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mie Goreng Ayam', 'price' => 18000, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ayam Bakar Taliwang', 'price' => 25000, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Es Teh Manis', 'price' => 5000, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kopi Hitam Arabika', 'price' => 10000, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Es Milo Dinosaur', 'price' => 12000, 'image' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Masukkan Pesanan Dummy Multi-Bulan untuk Testing Grafik Statistik
        DB::table('orders')->insert([
            ['id' => 1, 'table_number' => 3, 'customer_name' => 'Pelanggan 1', 'order_type' => 'dine', 'total_price' => 45000, 'payment_method' => 'Cash', 'amount_paid' => 45000, 'amount_change' => 0, 'created_at' => Carbon::now()->subMonths(1), 'updated_at' => Carbon::now()->subMonths(1)],
            ['id' => 2, 'table_number' => 0, 'customer_name' => 'Pelanggan 2', 'order_type' => 'takeaway', 'total_price' => 25000, 'payment_method' => 'Cash', 'amount_paid' => 25000, 'amount_change' => 0, 'created_at' => Carbon::now()->subMonths(1), 'updated_at' => Carbon::now()->subMonths(1)],
            ['id' => 3, 'table_number' => 2, 'customer_name' => 'Pelanggan 3', 'order_type' => 'dine', 'total_price' => 58000, 'payment_method' => 'Cash', 'amount_paid' => 58000, 'amount_change' => 0, 'created_at' => Carbon::now(), 'updated_at' => now()],
        ]);

        DB::table('order_details')->insert([
            ['order_id' => 1, 'menu_id' => 1, 'quantity' => 2, 'notes' => 'Pedas', 'price' => 20000],
            ['order_id' => 1, 'menu_id' => 4, 'quantity' => 1, 'notes' => '', 'price' => 5000],
            ['order_id' => 2, 'menu_id' => 3, 'quantity' => 1, 'notes' => 'Paha', 'price' => 25000],
            ['order_id' => 3, 'menu_id' => 2, 'quantity' => 2, 'notes' => 'Karet dua', 'price' => 18000],
            ['order_id' => 3, 'menu_id' => 6, 'quantity' => 1, 'notes' => '', 'price' => 12000],
            ['order_id' => 3, 'menu_id' => 4, 'quantity' => 2, 'notes' => 'Kurang manis', 'price' => 5000],
        ]);
    }
}
