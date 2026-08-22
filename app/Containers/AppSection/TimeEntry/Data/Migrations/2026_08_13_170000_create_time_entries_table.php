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
        Schema::create('time_entries', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->dateTime('started_at');
            $table->dateTime('stopped_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'started_at']);
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
