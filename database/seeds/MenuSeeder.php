<?php

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
            ],
            [
                'id'    => 2,
                'name'  => 'Profile',
                'sub_menu'  => null,
                'order'  => 2,
                'icon'  => 'flaticon2-writing',
                'url'  => 'personal-information',
                'id_element'  => 'research-profile-nav',
            ],
            [
                'id'    => 3,
                'name'  => 'Opportunity',
                'sub_menu'  => null,
                'order'  => 3,
                'icon'  => 'flaticon2-calendar-5',
                'url'  => 'research-opportunity',
                'id_element'  => 'opportunity-nav',
            ],
            [
                'id'    => 4,
                'name'  => 'Regulation',
                'sub_menu'  => null,
                'order'  => 4,
                'icon'  => 'flaticon2-protected',
                'url'  => 'regulation',
                'id_element'  => 'regulation-nav',
            ],
            [
                'id'    => 5,
                'name'  => 'Institution',
                'sub_menu'  => null,
                'order'  => 5,
                'icon'  => 'flaticon2-protection',
                'url'  => 'institution',
                'id_element'  => 'institution-nav',
            ],
            [
                'id'    => 6,
                'name'  => 'Research User',
                'sub_menu'  => null,
                'order'  => 6,
                'icon'  => 'flaticon2-user-outline-symbol',
                'url'  => 'research-user',
                'id_element'  => 'research-user-nav',
            ],
            [
                'id'    => 7,
                'name'  => 'Announcement',
                'sub_menu'  => null,
                'order'  => 7,
                'icon'  => 'la la-bullhorn',
                'url'  => 'announcement',
                'id_element'  => 'announcement-nav',
            ],
            [
                'id'    => 8,
                'name'  => 'Master',
                'sub_menu'  => null,
                'order'  => 8,
                'icon'  => 'flaticon2-layers-1',
                'url'  => null,
                'id_element'  => 'master-nav',
            ],
            [
                'id'    => 9,
                'name'  => 'Research Interest',
                'sub_menu'  => 8,
                'order'  => 9,
                'icon'  => null,
                'url'  => 'master-interest',
                'id_element'  => null,
            ],
            [
                'id'    => 10,
                'name'  => 'Skill & Expertise',
                'sub_menu'  => 8,
                'order'  => 10,
                'icon'  => null,
                'url'  => 'master-skill',
                'id_element'  => null,
            ],[
                'id'    => 11,
                'name'  => 'Country',
                'sub_menu'  => 8,
                'order'  => 11,
                'icon'  => null,
                'url'  => 'master-country',
                'id_element'  => null,
            ],[
                'id'    => 12,
                'name'  => 'Department',
                'sub_menu'  => 8,
                'order'  => 12,
                'icon'  => null,
                'url'  => 'master-departement',
                'id_element'  => null,
            ],[
                'id'    => 13,
                'name'  => 'Title',
                'sub_menu'  => 8,
                'order'  => 13,
                'icon'  => null,
                'url'  => 'master-interest',
                'id_element'  => null,
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
            ]);
        }
    }
}
