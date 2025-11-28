<?php
// database/seeders/BannerSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventBanner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            ['event_id' => 1, 'order' => 1, 'is_active' => true],
            ['event_id' => 2, 'order' => 2, 'is_active' => true],
            ['event_id' => 3, 'order' => 3, 'is_active' => true],
            ['event_id' => 4, 'order' => 4, 'is_active' => true],
            ['event_id' => 5, 'order' => 5, 'is_active' => true],
        ];

        foreach ($banners as $banner) {
            EventBanner::create([
                'event_id' => $banner['event_id'],
                'image' => 'banners/placeholder-' . $banner['event_id'] . '.jpg',
                'order' => $banner['order'],
                'is_active' => $banner['is_active'],
            ]);
        }
    }
}
