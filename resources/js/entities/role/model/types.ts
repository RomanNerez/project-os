/** Hashed id (RoleTransformer). */
export type RoleID = string;

/** A workspace role on the settings page (WorkspaceRoleTransformer). */
export interface WorkspaceRole {
    id: RoleID;
    name: string;
    display_name: string | null;
    description: string | null;
    /** Permission names, e.g. `projects.create`. */
    permissions: string[];
    users_count: number;
}

/** A permission a workspace role can grant (Authorization\Enums\Permission). */
export interface RolePermission {
    /** `<namespace>.<action>`, e.g. `projects.create`. */
    name: string;
    /** E.g. «Створення проєктів». */
    display_name: string;
}

export interface RoleDraft {
    display_name: string;
    description: string;
    permissions: string[];
}

export function emptyRoleDraft(): RoleDraft {
    return {
        display_name: '',
        description: '',
        permissions: [],
    };
}

export function toRoleDraft(role: WorkspaceRole): RoleDraft {
    return {
        display_name: role.display_name ?? role.name,
        description: role.description ?? '',
        permissions: [...role.permissions],
    };
}

export function roleLabel(role: Pick<WorkspaceRole, 'name' | 'display_name'>): string {
    return role.display_name ?? role.name;
}
