<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dateNow = Carbon::now();
        DB::table('users')->insert([
            'name' => 'User',
            'email' => 'user@email.com',
            'password' => bcrypt('password'),
            'created_at' => $dateNow,
            'updated_at' => $dateNow
        ]);
    }
}
