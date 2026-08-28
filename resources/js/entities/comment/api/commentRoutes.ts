import type { CommentID } from "../model/types";

export const commentRoutes = {
    update: (id: CommentID) => `/comment/${id}`,
    destroy: (id: CommentID) => `/comment/${id}`,
};