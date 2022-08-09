<?php

use Illuminate\Database\Seeder;

class DefaultTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $organizations = DB::table('organizations')->pluck('id');

        foreach ($organizations as $key => $organization) {
            $allTags = '';
            $allTags = [
                [
                    'name' => 'Emotions',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Popular',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ],
                [
                    'name' => 'Video',
                    'created_at' => now(),
                    'organization_id' => $organization,
                ]
            ];

            DB::table('all_tags')->insertOrIgnore($allTags);
        }
    }
}
