<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormConfigurationFormFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_configuration_form_field', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('form_configuration_id');
            $table->unsignedInteger('form_field_id');
            $table->unsignedInteger('order');

            $table->foreign('form_configuration_id')
                ->references('id')->on('form_configurations');
            $table->foreign('form_field_id')
                ->references('id')->on('form_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_configuration_form_field');
    }
}
