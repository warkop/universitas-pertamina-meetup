<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleData = [
            [
                'id'    => 1,
                'name'  => 'super_user',
                'type'  => '2',
                'package_id'  => null,
            ],
            [
                'id'    => 2,
                'name'  => 'regular_user',
                'type'  => '2',
                'package_id'  => null,
            ],
            [
                'id'    => 3,
                'name'  => 'institution',
                'type'  => '2',
                'package_id'  => null,
            ],
            [
                'id'    => 4,
                'name'  => 'Institusi - Free',
                'type'  => '0',
                'package_id'  => 1,
            ],
            [
                'id'    => 5,
                'name'  => 'Institusi - Silver',
                'type'  => '0',
                'package_id'  => 2,
            ],
            [
                'id'    => 6,
                'name'  => 'Institusi - Gold',
                'type'  => '0',
                'package_id'  => 3,
            ],
            [
                'id'    => 7,
                'name'  => 'Research Independent - Free',
                'type'  => '1',
                'package_id'  => 1,
            ],
            [
                'id'    => 8,
                'name'  => 'Research Independent - Silver',
                'type'  => '1',
                'package_id'  => 2,
            ],
            [
                'id'    => 9,
                'name'  => 'Default Research - Free',
                'type'  => '3',
                'package_id'  => 1,
            ],
            [
                'id'    => 10,
                'name'  => 'Default Research - Silver',
                'type'  => '3',
                'package_id'  => 2,
            ],
            [
                'id'    => 11,
                'name'  => 'Default Research - Gold',
                'type'  => '3',
                'package_id'  => 3,
            ],
        ];

        foreach ($roleData as $key) {
            Role::updateOrCreate([
                'id' => $key['id']
            ],[
                'id'    => $key['id'],
                'name'  => $key['name'],
                'type'  => $key['type'],
                'package_id'  => $key['package_id']
            ]);
        }
    }
}
