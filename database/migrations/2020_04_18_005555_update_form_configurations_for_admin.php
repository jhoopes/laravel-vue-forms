<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFormConfigurationsForAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_configurations', function (Blueprint $table) {
            $table->string('type')->nullable()->after('name');
            $table->boolean('active')->default(1)->after('type');
            $table->json('options')->nullable()->after('entity_model');

            $table->string('entity_name')->nullable()->change();
            $table->string('entity_model')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('form_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'active',
                'options'
            ]);

            $table->string('entity_name')->nullable(false)->change();
            $table->string('entity_model')->nullable(false)->change();
        });
    }
}
