<?php

use App\Containers\AppSection\Finance\Enums\FinanceStatus;
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
        Schema::create('finances', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type', 16);
            $table->string('currency', 3);
            $table->decimal('amount', 14, 2);
            $table->decimal('exchange_rate', 12, 4)->nullable();
            $table->decimal('amount_uah', 14, 2);
            $table->string('status', 16)->default(FinanceStatus::PLANNED->value);
            $table->date('due_date')->nullable();
            $table->date('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('finances');
    }
};
