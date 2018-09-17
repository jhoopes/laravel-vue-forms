<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('form_field_id');
            $table->unsignedInteger('entity_id');
            $table->string('entity_type');
            $table->longText('value');

            $table->timestamps();
            if(config('laravel-vue-forms.values_soft_delete')) {
                $table->softDeletes();
            }

            $table->foreign('form_field_id')->references('id')->on('form_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_values');
    }
}
