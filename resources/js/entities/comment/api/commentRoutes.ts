import type { CommentID } from "../model/types";

export const commentRoutes = {
    destroy: (id: CommentID) => `/comment/${id}`,
};