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
        Schema::create('finance_task', static function (Blueprint $table) {
            $table->foreignId('finance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();

            $table->primary(['finance_id', 'task_id']);
            $table->index('task_id');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_task');
    }
};
