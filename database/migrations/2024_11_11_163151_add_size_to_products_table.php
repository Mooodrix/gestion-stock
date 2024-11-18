<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migration
        public function up()
        {
            Schema::table('products', function (Blueprint $table) {
                $table->string('size')->nullable(); // Le champ size est une chaîne de caractères
            });
        }

        public function down()
        {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('size');
            });
        }

    
};
