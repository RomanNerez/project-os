<?php

namespace App\Containers\AppSection\Authorization\Enums;

/**
 * Workspace-level permissions granted through workspace roles (guard "web").
 *
 * Values are "<section>.<action>"; after adding a case run the permissions seeder.
 */
enum Permission: string
{
    public const GUARD = 'web';

    case PROJECTS_CREATE = 'projects.create';
    case TEAM_VIEW = 'team.view';

    public function label(): string
    {
        return match ($this) {
            self::PROJECTS_CREATE => 'Створення проєктів',
            self::TEAM_VIEW => 'Перегляд команди',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
