type UserID = number;

export interface User {
    id: UserID;
    name: string;
    email: string;
    avatar_url: string | null;
}

/** The current user as the profile page receives it (UserTransformer). */
export interface UserProfile extends User {
    first_name: string | null;
    last_name: string | null;
    email_verified_at: string | null;
    gender: string | null;
    birth: string | null;
}
