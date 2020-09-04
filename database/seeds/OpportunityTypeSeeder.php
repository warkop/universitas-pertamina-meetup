<?php

use App\Model\OpportunityType;
use Illuminate\Database\Seeder;

class OpportunityTypeSeeder extends Seeder
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
                'name'  => 'Research',
            ],
            [
                'id'    => 2,
                'name'  => 'Funding',
            ],
        ];

        foreach ($roleData as $key) {
            OpportunityType::updateOrCreate([
                'id' => $key['id']
            ],[
                'id'    => $key['id'],
                'name'  => $key['name']
            ]);
        }
    }
}
