<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::updateOrCreate([
            'employee_id_number' => 3042311060,
        ],[
            'login' => 3042311060,
            'full_name' => 'SALIMOV BEHRUZBEK SOBIROVICH',
            'short_name' => 'SALIMOV B.S',
            'password' => 3042311060
        ]);
        UserProfile::updateOrCreate([
            'user_id' => $teacher->id,
        ],[
            'department_id' => 16
        ]);

        $teacher->assignRole('teacher');
    }
}
