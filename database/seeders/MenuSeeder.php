<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
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
                'name'  => 'Home',
                'sub_menu'  => null,
                'order'  => 1,
                'icon'  => 'flaticon2-architecture-and-city',
                'url'  => 'home',
                'id_element'  => 'home-nav',
                'action' => 'R'
            ],
            [
                'id'    => 2,
                'name'  => 'Profile',
                'sub_menu'  => null,
                'order'  => 2,
                'icon'  => 'flaticon2-writing',
                'url'  => 'personal-information',
                'id_element'  => 'research-profile-nav',
                'action' => 'R,U'
            ],
            [
                'id'    => 3,
                'name'  => 'Opportunity',
                'sub_menu'  => null,
                'order'  => 3,
                'icon'  => 'flaticon2-calendar-5',
                'url'  => 'opportunity',
                'id_element'  => 'opportunity-nav',
                'action' => 'C,R,U,D,DE'
            ],
            [
                'id'    => 4,
                'name'  => 'Regulation',
                'sub_menu'  => null,
                'order'  => 4,
                'icon'  => 'flaticon2-protected',
                'url'  => 'regulation',
                'id_element'  => 'regulation-nav',
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 5,
                'name'  => 'Institution',
                'sub_menu'  => null,
                'order'  => 5,
                'icon'  => 'flaticon2-protection',
                'url'  => 'master-institution',
                'id_element'  => 'institution-nav',
                'action' => 'C,R,U,D,A'
            ],
            [
                'id'    => 6,
                'name'  => 'Research User',
                'sub_menu'  => null,
                'order'  => 6,
                'icon'  => 'flaticon2-user-outline-symbol',
                'url'  => 'research-user',
                'id_element'  => 'research-user-nav',
                'action' => 'C,R,U,D,I,A'
            ],
            [
                'id'    => 7,
                'name'  => 'Announcement',
                'sub_menu'  => null,
                'order'  => 7,
                'icon'  => 'la la-bullhorn',
                'url'  => 'announcement',
                'id_element'  => 'announcement-nav',
                'action' => 'R'
            ],
            [
                'id'    => 8,
                'name'  => 'Master',
                'sub_menu'  => null,
                'order'  => 8,
                'icon'  => 'flaticon2-layers-1',
                'url'  => null,
                'id_element'  => 'master-nav',
                'action' => null
            ],
            [
                'id'    => 9,
                'name'  => 'Research Interest',
                'sub_menu'  => 8,
                'order'  => 9,
                'icon'  => null,
                'url'  => 'master-interest',
                'id_element'  => null,
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 10,
                'name'  => 'Skill & Expertise',
                'sub_menu'  => 8,
                'order'  => 10,
                'icon'  => null,
                'url'  => 'master-skill',
                'id_element'  => null,
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 11,
                'name'  => 'Country',
                'sub_menu'  => 8,
                'order'  => 11,
                'icon'  => null,
                'url'  => 'master-country',
                'id_element'  => null,
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 12,
                'name'  => 'Department',
                'sub_menu'  => 8,
                'order'  => 12,
                'icon'  => null,
                'url'  => 'master-department',
                'id_element'  => null,
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 13,
                'name'  => 'Role',
                'sub_menu'  => 8,
                'order'  => 13,
                'icon'  => null,
                'url'  => 'user-role',
                'id_element'  => null,
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 14,
                'name'  => 'Title',
                'sub_menu'  => 8,
                'order'  => 14,
                'icon'  => null,
                'url'  => 'master-interest',
                'id_element'  => null,
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 15,
                'name'  => 'Academic Degree',
                'sub_menu'  => 8,
                'order'  => 15,
                'icon'  => null,
                'url'  => 'master-degree',
                'id_element'  => null,
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 16,
                'name'  => 'Publication Type',
                'sub_menu'  => 8,
                'order'  => 16,
                'icon'  => null,
                'url'  => 'publication-type',
                'id_element'  => null,
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 17,
                'name'  => 'Research Group',
                'sub_menu'  => null,
                'order'  => 17,
                'icon'  => 'la la-group',
                'url'  => 'research-group',
                'id_element'  => 'research-group-nav',
                'action' => 'C,R,U,D'
            ],
            [
                'id'    => 18,
                'name'  => 'Payment',
                'sub_menu'  => null,
                'order'  => 18,
                'icon'  => 'flaticon2-list',
                'url'  => 'histori-payment',
                'id_element'  => 'histori-payment-nav',
                'action' => 'R,DE'
            ],
            [
                'id'    => 19,
                'name'  => 'Invoice',
                'sub_menu'  => null,
                'order'  => 19,
                'icon'  => 'flaticon2-list',
                'url'  => 'payment-sysadmin',
                'id_element'  => 'payment-sysadmin-nav',
                'action' => 'R,A,DE'
            ],
        ];

        foreach ($roleData as $key) {
            Menu::updateOrCreate([
                'id' => $key['id']
            ],[
                'id'    => $key['id'],
                'name'  => $key['name'],
                'sub_menu'  => $key['sub_menu'],
                'order'  => $key['order'],
                'icon'  => $key['icon'],
                'url'  => $key['url'],
                'id_element'  => $key['id_element'],
                'action'  => $key['action'],
            ]);
        }
    }
}
