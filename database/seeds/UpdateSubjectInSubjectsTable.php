<?php

use Illuminate\Database\Seeder;

class UpdateSubjectInSubjectsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subjects')->truncate();
        $organizations = DB::table('organizations')->pluck('id');

        foreach ($organizations as $key => $organization) {
            $educationLevels = '';

            $educationLevels = [
                [
                    'name' => 'Self Awareness',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Self Management',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Relationship Skills',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Responsible Decision Making',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Social Awareness',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
            ];
            DB::table('subjects')->insertOrIgnore($educationLevels);
        }
    }
}
