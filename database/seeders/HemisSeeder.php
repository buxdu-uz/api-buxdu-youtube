<?php

namespace Database\Seeders;

use App\Domain\Departments\Models\Department;
use App\Domain\Faculties\Models\Faculty;
use App\Domain\Subjects\Models\Subject;
use App\Models\User;
use App\Models\UserProfile;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class HemisSeeder extends Seeder
{
    /**
     * @var mixed|Client
     */
    public mixed $clients;

    /**
     * @var mixed|string[]
     */
    public mixed $headers;

    public function __construct()
    {
        $this->clients = new Client();
        $this->headers = [
            'Authorization' => 'Bearer ' . config('hemis.api_key'),
            'Accept' => 'application/json',
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Seeding faculties...');
        $this->faculties();

        $this->command?->info('Seeding departments...');
        $this->departments();

        $this->command?->info('Seeding subjects...');
        $this->subjects();

        $this->command?->info('Seeding admin...');
        $this->admin();

        $this->command?->info('Seeding teachers...');
        $this->teachers();

        $this->command?->info('Hemis seeding completed.');
    }

    /**
     * @return void
     */
    public function admin()
    {
        $roleAdmin = Role::updateOrcreate([
            'name' => 'admin',
        ]);

        Role::updateOrcreate([
            'name' => 'teacher',
        ]);

        $user = User::updateOrCreate([
            'id' => 1,
            'employee_id_number' => 1,
        ],[
            'login' => 'admin',
            'full_name' => 'Admin',
            'short_name' => 'Admin',
            'password' => 'adminatm2025',
        ]);

        $teacher = User::updateOrCreate([
            'id' => 2,
            'employee_id_number' => 3042311060,
        ],[
            'login' => 3042311060,
            'full_name' => 'SALIMOV BEHRUZBEK SOBIROVICH',
            'short_name' => 'SALIMOV B.S',
            'password' => 3042311060
        ]);

        UserProfile::updateOrCreate([
            'user_id' => $user->id,
        ]);
        UserProfile::updateOrCreate([
            'user_id' => $teacher->id,
        ]);

        $user->assignRole($roleAdmin->name);
        $teacher->assignRole('teacher');
    }

    /**
     * @return void
     */
    public function teachers()
    {
        $request = new Request('GET', config('hemis.host').'data/employee-list?type=teacher&limit='.config('hemis.limit'), $this->headers);
        $res = $this->clients->sendAsync($request)->wait();
        $resBody = json_decode($res->getBody());

        if (isset($resBody->data->pagination->pageCount) && isset($resBody->data->items)) {
            foreach ($resBody->data->items as $dt) {

                $department = Department::query()
                    ->find($dt->department->id);

                if ($department) {
                    $user = User::updateOrCreate([
                        'id' => $dt->id,
                        'employee_id_number' => $dt->employee_id_number,
                    ],[
                        'login' => $dt->employee_id_number,
                        'full_name' => $dt->full_name,
                        'short_name' => $dt->short_name,
                        'password' => $dt->employee_id_number,
                    ]);

                    UserProfile::updateOrCreate([
                        'user_id' => $user->id,
                    ],[
                        'department_id' => $dt->department->id,
                        'avatar' => $dt->image,
                    ]);

                    $user->assignRole('teacher');
                }
            }

            // Loop through the remaining pages
            for ($i = 2; $i <= $resBody->data->pagination->pageCount; $i++) {
                $request = new Request(
                    'GET',
                    config('hemis.host') . 'data/employee-list?type=teacher&limit='.config('hemis.limit').'&page=' . $i,
                    $this->headers
                );
                $res = $this->clients->sendAsync($request)->wait();
                $resBody = json_decode($res->getBody());

                // Check if the request was successful
                if (isset($resBody->data->items)) {
                    foreach ($resBody->data->items as $dt) {
                        $department = Department::query()
                            ->find($dt->department->id);
                        if ($department) {
                            $user = User::updateOrCreate([
                                'id' => $dt->id,
                                'employee_id_number' => $dt->employee_id_number,
                            ],[
                                'login' => $dt->employee_id_number,
                                'full_name' => $dt->full_name,
                                'short_name' => $dt->short_name,
                                'password' => $dt->employee_id_number,
                            ]);

                            UserProfile::updateOrCreate([
                                'user_id' => $user->id,
                            ],[
                                'department_id' => $dt->department->id,
                                'avatar' => $dt->image,
                            ]);

                            $user->assignRole('teacher');
                        }
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function faculties()
    {
        $request = new Request('GET', config('hemis.host').'data/department-list?_structure_type=11&limit='.config('hemis.limit'), $this->headers);
        $res = $this->clients->sendAsync($request)->wait();
        $resBody = json_decode($res->getBody());

        if (isset($resBody->data->pagination->pageCount) && isset($resBody->data->items)) {
            foreach ($resBody->data->items as $dt) {
                if (strpos($dt->name, 'nofaol') === false) {
                    Faculty::updateOrCreate([
                        'id' => $dt->id,
                    ],[
                        'name' => $dt->name,
                        'code' => $dt->code,
                    ]);
                }
            }

            // Loop through the remaining pages
            for ($i = 2; $i <= $resBody->data->pagination->pageCount; $i++) {
                $request = new Request(
                    'GET',
                    config('hemis.host') . 'data/department-list?_structure_type=11&limit='.config('hemis.limit').'&page=' . $i,
                    $this->headers
                );
                $res = $this->clients->sendAsync($request)->wait();
                $resBody = json_decode($res->getBody());

                // Check if the request was successful
                if (isset($resBody->data->items)) {
                    foreach ($resBody->data->items as $dt) {
                        if (strpos($dt->name, 'nofaol') === false) {
                            Faculty::updateOrCreate([
                                'id' => $dt->id,
                            ],[
                                'name' => $dt->name,
                                'code' => $dt->code,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function departments()
    {
        $request = new Request('GET', config('hemis.host').'data/department-list?_structure_type=12&limit='.config('hemis.limit'), $this->headers);
        $res = $this->clients->sendAsync($request)->wait();
        $resBody = json_decode($res->getBody());

        if (isset($resBody->data->pagination->pageCount) && isset($resBody->data->items)) {
            foreach ($resBody->data->items as $dt) {
                if (strpos($dt->name, 'nofaol') === false) {
                    Department::updateOrCreate([
                        'id' => $dt->id,
                    ],[
                        'faculty_id' => $dt->parent,
                        'name' => $dt->name,
                        'code' => $dt->code,
                    ]);
                }
            }

            // Loop through the remaining pages
            for ($i = 2; $i <= $resBody->data->pagination->pageCount; $i++) {
                $request = new Request(
                    'GET',
                    config('hemis.host') . 'data/department-list?_structure_type=12&limit='.config('hemis.limit').'&page=' . $i,
                    $this->headers
                );
                $res = $this->clients->sendAsync($request)->wait();
                $resBody = json_decode($res->getBody());

                // Check if the request was successful
                if (isset($resBody->data->items)) {
                    foreach ($resBody->data->items as $dt) {
                        if (strpos($dt->name, 'nofaol') === false) {
                            Department::updateOrCreate([
                                'id' => $dt->id,
                            ],[
                                'faculty_id' => $dt->parent,
                                'name' => $dt->name,
                                'code' => $dt->code,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function subjects()
    {
        $request = new Request('GET', config('hemis.host').'data/subject-meta-list?limit='.config('hemis.limit'), $this->headers);
        $res = $this->clients->sendAsync($request)->wait();
        $resBody = json_decode($res->getBody());

        if (isset($resBody->data->pagination->pageCount) && isset($resBody->data->items)) {
            foreach ($resBody->data->items as $dt) {
                if ($dt->active == true) {
                    Subject::updateOrCreate([
                        'id' => $dt->id,
                    ],[
                        'name' => $dt->name,
                        'code' => $dt->code,
                    ]);
                }
            }

            // Loop through the remaining pages
            for ($i = 2; $i <= $resBody->data->pagination->pageCount; $i++) {
                $request = new Request(
                    'GET',
                    config('hemis.host') . 'data/subject-meta-list?&limit='.config('hemis.limit').'&page=' . $i,
                    $this->headers
                );
                $res = $this->clients->sendAsync($request)->wait();
                $resBody = json_decode($res->getBody());

                // Check if the request was successful
                if (isset($resBody->data->items)) {
                    foreach ($resBody->data->items as $dt) {
                        if ($dt->active == true) {
                            Subject::updateOrCreate([
                                'id' => $dt->id,
                            ],[
                                'name' => $dt->name,
                                'code' => $dt->code,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
