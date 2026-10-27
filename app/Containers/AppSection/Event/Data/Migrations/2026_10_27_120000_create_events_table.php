<?php

use App\Containers\AppSection\Event\Enums\EventType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('type', 32)->default(EventType::MEETING->value);
            $table->string('color', 7)->nullable();
            $table->timestampTz('start_at');
            $table->timestampTz('end_at')->nullable();
            $table->boolean('all_day')->default(false);
            $table->unsignedInteger('reminder_minutes')->nullable();
            $table->timestampTz('reminded_at')->nullable();
            $table->timestamps();

            $table->index('start_at');
            $table->index(['project_id', 'start_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
