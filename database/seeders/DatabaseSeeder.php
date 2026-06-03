<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. Akun Admin Utama

        \App\Models\User::create([
            'name' => 'Admin Amikom',
            'email' => 'admin@amikom.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 2. Insert Kategori Event

        $category = \App\Models\Category::create([
            'name' => 'Seminar IT',
            'slug' => 'seminar-it',
        ]);

        $category2 = \App\Models\Category::firstOrCreate([
            'name' => 'Entertaiment',
            'slug' => 'entertaiment',
        ]);

        $category3 = \App\Models\Category::firstOrCreate([
            'name' => 'Competition',
            'slug' => 'competition',
        ]);


        // 3. Insert Sampel Events

        \App\Models\Event::create([
            'category_id' => $category2->id,
            'title' => 'Jazz Night 2025',
            'description' => 'Nikmati malam yang indah dengan alunan musik jazz yang merdu.',
            'date' => '2026-05-10 19:00:00',
            'location' => 'Amikom Baru',
            'price' => 50000,
            'stock' => 100,
            'poster_path' => 'posters/event-1.png'
        ]);

        \App\Models\Event::create([
            'category_id' => $category->id,
            'title' => 'Hackaton - Unleash Your Inner Developer',
            'description' => 'Ayo asah skill coding kamu dan ciptakan solusi inovatif untuk tantangan masa depan',
            'date' => '2026-05-05 10:00:00',
            'location' => 'Inkubator Amikom',
            'price' => 50000,
            'stock' => 100,
            'poster_path' => 'posters/event-2.png',
        ]);

        \App\Models\Event::create([
            'category_id' => $category->id,
            'title' => 'AI % FUTURE TECH SUMMIT 2026',
            'description' => 'Jelajahi tren terkini dalam kecerdasan buatan dan teknologi masa depan bersama para ahli di bidangnya.',
            'date' => '2026-05-01 13:00:00',
            'location' => 'Cinema Unit 6',
            'price' => 50000,
            'stock' => 100,
            'poster_path' => 'posters/event-3.png',
        ]);

        \App\Models\Event::create([
            'category_id' => $category3->id,
            'title' => 'GEMASI AMIKOM 2026',
            'description' => 'Ayo ikuti kompetisi Gelar Karya Mahasiswa Sistem Informasi Universitas Amikom Yogyakarta, Ada banyak kategori yang menarik',
            'date' => '2026-02-01 12:00:00',
            'location' => 'Online',
            'price' => 5000,
            'stock' => 100,
            'poster_path' => 'posters/event-4.png',
        ]);

        \App\Models\Event::create([
            'category_id' => $category3->id,
            'title' => 'AMICTA AMIKOM 2026',
            'description' => 'Buat kamu Mahasiswa Universitas Amikom Yogyakarta, Ayo ikuti kompetisi bergengsi AMICTA! ',
            'date' => '2026-07-01 08:00:00',
            'location' => 'Ruang Citra 2',
            'price' => 10000,
            'stock' => 100,
            'poster_path' => 'posters/event-5.png',
        ]);

        \App\Models\Event::create([
            'category_id' => $category->id,
            'title' => 'Seminar Internasional Trading',
            'description' => 'Ikuti seminar internasional tentang dunia trading yang menarik',
            'date' => '2026-05-01 13:00:00',
            'location' => 'Ruang Citra 1',
            'price' => 25000,
            'stock' => 100,
            'poster_path' => 'posters/event-6.png',
        ]);
        
    }
}
