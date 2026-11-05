<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Every row is now "task + user": how many seconds of the user's tracked time the record pays for,
     * and the user's hourly rate (always USD) copied from project_user when the record was saved,
     * so later rate changes do not rewrite past payments.
     *
     * @return void
     */
    public function up(): void
    {
        // Existing rows have no user and cannot satisfy NOT NULL.
        DB::table('finance_task')->delete();

        Schema::table('finance_task', static function (Blueprint $table) {
            $table->dropPrimary(['finance_id', 'task_id']);

            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('seconds')->default(0);
            $table->decimal('hourly_rate', 10, 2)->nullable()->comment('USD per hour, snapshot of project_user.hourly_rate');

            $table->primary(['finance_id', 'task_id', 'user_id']);
            $table->index(['task_id', 'user_id']);
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        DB::table('finance_task')->delete();

        Schema::table('finance_task', static function (Blueprint $table) {
            $table->dropPrimary(['finance_id', 'task_id', 'user_id']);
            $table->dropIndex(['task_id', 'user_id']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['seconds', 'hourly_rate']);

            $table->primary(['finance_id', 'task_id']);
        });
    }
};
