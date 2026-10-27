import type { IncludedData } from '@/shared/types';

type UserID = number;

export interface User {
    id: UserID;
    name: string;
    email: string;
    avatar_url: string | null;
    /** Only on the shared `auth.user` (HandleInertiaRequests). */
    permissions?: {
        manage_users: boolean;
    };
}

/** The current user as the profile page receives it (UserTransformer). */
export interface UserProfile extends User {
    first_name: string | null;
    last_name: string | null;
    email_verified_at: string | null;
    gender: string | null;
    birth: string | null;
}

/** RoleTransformer. */
export interface UserRole {
    id: string;
    name: string;
    display_name: string | null;
}

/** A row of the users list (UserAdminTransformer with roles). */
export interface UserListItem extends UserProfile {
    created_at: string;
    roles: IncludedData<UserRole[]>;
}

/** A pending invitation on the users page (InvitationTransformer). */
export interface UserInvitation {
    id: number;
    email: string;
    invited_by_name: string | null;
    expires_at: string;
    created_at: string;
}

export interface UserDraft {
    first_name: string;
    last_name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

export function emptyUserDraft(): UserDraft {
    return {
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
    };
}
