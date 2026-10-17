<?php

use App\Containers\AppSection\Task\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Tasks always belong to a project now; project-less tasks are dropped.
     * Deleted through the model so polymorphic comments and media (incl. files) go too.
     *
     * @return void
     */
    public function up(): void
    {
        Task::query()->whereNull('project_id')->lazyById()->each(static function (Task $task): void {
            $task->comments()->delete();
            $task->delete();
        });

        Schema::table('tasks', static function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('tasks', static function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });
    }
};
