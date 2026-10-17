<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::table('time_entries', static function (Blueprint $table) {
            $table->foreignId('task_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('time_entries', static function (Blueprint $table) {
            $table->dropConstrainedForeignId('task_id');
        });
    }
};
