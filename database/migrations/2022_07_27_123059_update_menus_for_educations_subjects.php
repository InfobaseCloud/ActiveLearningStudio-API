<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMenusForEducationsSubjects extends Migration
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
            '--class' => UpdateEducationsInEducationTable::class,
            '--force' => true
        ]);
        \Artisan::call('db:seed', [
            '--class' => UpdateSubjectInSubjectsTable::class,
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
