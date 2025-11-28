<?php
// database/seeders/EventSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Tech Conference 2025',
                'description' => 'Join us for the biggest tech conference of the year! Learn about the latest trends in AI, Web Development, and Cloud Computing. Network with industry leaders and innovate together.',
                'location' => 'Jakarta Convention Center',
                'price' => 500000,
                'quota' => 500,
                'quota_remaining' => 450,
                'event_date' => Carbon::now()->addDays(30),
                'status' => 'published',
            ],
            [
                'title' => 'Music Festival Wonderland',
                'description' => 'Experience 3 days of non-stop music with world-class artists. Food courts, camping areas, and amazing vibes await you!',
                'location' => 'GBK Senayan, Jakarta',
                'price' => 750000,
                'quota' => 10000,
                'quota_remaining' => 8500,
                'event_date' => Carbon::now()->addDays(45),
                'status' => 'published',
            ],
            [
                'title' => 'Startup Pitch Competition',
                'description' => 'Calling all entrepreneurs! Pitch your startup idea and win funding up to $100,000. Mentorship sessions included.',
                'location' => 'Bandung Digital Valley',
                'price' => 200000,
                'quota' => 100,
                'quota_remaining' => 75,
                'event_date' => Carbon::now()->addDays(20),
                'status' => 'published',
            ],
            [
                'title' => 'Food & Culinary Expo',
                'description' => 'Taste dishes from over 200 vendors! Cooking workshops, celebrity chef demos, and food competitions.',
                'location' => 'Surabaya Expo Center',
                'price' => 150000,
                'quota' => 2000,
                'quota_remaining' => 1800,
                'event_date' => Carbon::now()->addDays(15),
                'status' => 'published',
            ],
            [
                'title' => 'Digital Marketing Masterclass',
                'description' => 'Master SEO, Social Media Marketing, and Google Ads. Certification provided. Limited seats!',
                'location' => 'Online (Zoom)',
                'price' => 300000,
                'quota' => 200,
                'quota_remaining' => 50,
                'event_date' => Carbon::now()->addDays(10),
                'status' => 'published',
            ],
            [
                'title' => 'Art & Design Exhibition',
                'description' => 'Explore contemporary art from Southeast Asian artists. Interactive installations and workshops.',
                'location' => 'National Gallery, Jakarta',
                'price' => 100000,
                'quota' => 500,
                'quota_remaining' => 20,
                'event_date' => Carbon::now()->addDays(7),
                'status' => 'published',
            ],
            [
                'title' => 'Gaming Championship 2025',
                'description' => 'Compete in Mobile Legends, PUBG, and Valorant tournaments. Total prize pool: 500 million rupiah!',
                'location' => 'Bali International Convention Center',
                'price' => 250000,
                'quota' => 1000,
                'quota_remaining' => 800,
                'event_date' => Carbon::now()->addDays(60),
                'status' => 'published',
            ],
            [
                'title' => 'Yoga & Wellness Retreat',
                'description' => '3-day wellness retreat with yoga, meditation, spa treatments, and organic meals.',
                'location' => 'Ubud, Bali',
                'price' => 2500000,
                'quota' => 50,
                'quota_remaining' => 30,
                'event_date' => Carbon::now()->addDays(40),
                'status' => 'published',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
