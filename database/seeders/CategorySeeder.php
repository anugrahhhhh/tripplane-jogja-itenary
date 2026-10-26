<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Restoran & Kuliner', 'slug' => 'kuliner', 'icon' => 'kuliner'],
            ['name' => 'Budaya & Sejarah', 'slug' => 'budaya-sejarah', 'icon' => 'budaya-sejarah'],
            ['name' => 'Ruang Publik & Aktivitas Malam', 'slug' => 'ruang-publik-malam', 'icon' => 'ruang-publik-malam'],
            ['name' => 'Penginapan', 'slug' => 'penginapan', 'icon' => 'penginapan'],
            ['name' => 'Belanja & Oleh-oleh', 'slug' => 'belanja-oleh-oleh', 'icon' => 'belanja-oleh-oleh'],
            ['name' => 'Kampung Wisata & Kerajinan', 'slug' => 'kampung-wisata-kerajinan', 'icon' => 'kampung-wisata-kerajinan'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
