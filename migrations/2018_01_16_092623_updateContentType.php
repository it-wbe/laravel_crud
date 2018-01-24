<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('content_type')) {
            if (!Schema::hasColumn('content_type', 'need_meta')) {
                Schema::table('content_type', function (Blueprint $table) {
                    $table->smallInteger('need_meta')->default(0);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('content_type')) {
            if (Schema::hasColumn('content_type', 'need_meta')) {
                Schema::table('content_type', function ($table) {
                    $table->dropColumn('need_meta');
                });
            }
        }
    }
}
