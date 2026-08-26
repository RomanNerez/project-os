type UserID = number;

export interface User {
    id: UserID;
    name: string;
    email: string;
    email_verified_at: string;
    gender: string;
    birth: string;
}