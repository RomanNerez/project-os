import type { IncludedData } from "@/shared/types";

export type CommentID = number;

export interface Comment {
    id: CommentID;
    user_id: number;
    body: string;
    created_at: string;
    updated_at: string;
}

export interface CommentIncludes<TUser = null> extends Comment {
    user: IncludedData<TUser>;
}