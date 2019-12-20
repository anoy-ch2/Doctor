<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientListsTable extends Migration
{
    public function up()
    {
        Schema::create('patient_lists', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable();

            $table->longText('address')->nullable();

            $table->string('email')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });
    }
}
