<?php

use Illuminate\Database\Migrations\Migration;

class UpdateTeamRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        \Artisan::call('db:seed', [
            '--class' => TeamRolePermissionSeeder::class,
            '--force' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
