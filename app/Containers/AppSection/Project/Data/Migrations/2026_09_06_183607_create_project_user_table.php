<?php

use App\Containers\AppSection\Project\Enums\ProjectRole;
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
        Schema::create('project_user', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table
                ->enum('role', array_values(ProjectRole::cases()))
                ->default(ProjectRole::MEMBER->value);
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
