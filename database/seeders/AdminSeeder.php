<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Yaroslav',
                'email' => 'ym@vanity.ai',
                'password' => 'secret',
                'timezone' => 'Europe/Kiev',
            ],
            [
                'name' => 'Pasha',
                'email' => 'pp@vanity.ai',
                'password' => 'secret',
                'timezone' => 'Europe/Kiev',
            ],
            [
                'name' => 'Yura',
                'email' => 'yf@vanity.ai',
                'password' => 'secret',
                'timezone' => 'UTC',
            ],
            [
                'name' => 'Nazar',
                'email' => 'ng@vanity.ai',
                'password' => 'secret',
                'timezone' => 'UTC',
            ],
            [
                'name' => 'Alisa',
                'email' => 'ak@vanity.ai',
                'password' => 'secret',
                'timezone' => 'UTC',
            ],
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}
