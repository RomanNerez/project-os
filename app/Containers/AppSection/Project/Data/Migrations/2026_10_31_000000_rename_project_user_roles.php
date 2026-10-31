<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $this->replaceRoles(['member' => 'executor', 'viewer' => 'client'], 'executor');
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->replaceRoles(['executor' => 'member', 'client' => 'viewer'], 'member');
    }

    /**
     * @param array<string, string> $renames old role => new role
     */
    private function replaceRoles(array $renames, string $default): void
    {
        $values = implode(', ', array_map(static fn(string $role): string => "'{$role}'", ['admin', ...array_values($renames)]));

        DB::statement('ALTER TABLE project_user DROP CONSTRAINT IF EXISTS project_user_role_check');

        foreach ($renames as $from => $to) {
            DB::table('project_user')->where('role', $from)->update(['role' => $to]);
        }

        DB::statement("ALTER TABLE project_user ADD CONSTRAINT project_user_role_check CHECK (role IN ({$values}))");
        DB::statement("ALTER TABLE project_user ALTER COLUMN role SET DEFAULT '{$default}'");
    }
};
