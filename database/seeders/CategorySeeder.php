<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name_ar' => 'المأكولات المنزلية', 'name_en' => 'Home-made Foods', 'slug' => 'home-food', 'icon' => 'fa-utensils'],
            ['name_ar' => 'الحلويات والمعجنات', 'name_en' => 'Sweets & Pastries', 'slug' => 'sweets', 'icon' => 'fa-cookie-bite'],
            ['name_ar' => 'العطور والبخور', 'name_en' => 'Perfumes & Incense', 'slug' => 'perfumes', 'icon' => 'fa-bottle-droplet'],
            ['name_ar' => 'الاكسسوارات', 'name_en' => 'Accessories', 'slug' => 'accessories', 'icon' => 'fa-gem'],
            ['name_ar' => 'مستحضرات التجميل والعناية', 'name_en' => 'Cosmetics & Skincare', 'slug' => 'cosmetics', 'icon' => 'fa-spa'],
            ['name_ar' => 'إلكترونيات', 'name_en' => 'Electronics', 'slug' => 'electronics', 'icon' => 'fa-laptop'],
            ['name_ar' => 'أعمال يدوية', 'name_en' => 'Handicrafts', 'slug' => 'handicrafts', 'icon' => 'fa-palette'],
            ['name_ar' => 'الأزياء والملابس', 'name_en' => 'Fashion & Clothing', 'slug' => 'fashion', 'icon' => 'fa-shirt'],
            ['name_ar' => 'الألعاب', 'name_en' => 'Toys & Games', 'slug' => 'toys', 'icon' => 'fa-gamepad'],
        ];

        \Illuminate\Support\Facades\DB::table('categories')->truncate();
        \Illuminate\Support\Facades\DB::table('categories')->insert($categories);
    }
}
