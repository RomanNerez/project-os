<?php

use App\Containers\AppSection\Task\Enums\TaskPriority;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Estimate is the total for everyone working on the task, in minutes.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('tasks', static function (Blueprint $table) {
            $table->string('priority', 32)->default(TaskPriority::MEDIUM->value)->after('status');
            $table->unsignedInteger('estimate')->nullable()->after('priority');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('tasks', static function (Blueprint $table) {
            $table->dropColumn(['priority', 'estimate']);
        });
    }
};
