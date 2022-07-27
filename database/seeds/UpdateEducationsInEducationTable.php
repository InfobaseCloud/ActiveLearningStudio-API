<?php

use Illuminate\Database\Seeder;

class UpdateEducationsInEducationTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('education_levels')->truncate();
        $organizations = DB::table('organizations')->pluck('id');

        foreach ($organizations as $key => $organization) {
            $educationLevels = '';

            $educationLevels = [
                [
                    'name' => 'Middle School (6th - 8th)',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Lower High School (9th-10th)',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Upper High School (11th-12th)',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Parents',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Teachers',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
            ];

            DB::table('education_levels')->insertOrIgnore($educationLevels);
        }
    }
}
