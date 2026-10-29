import type { CommentID } from "../model/types";

export const commentRoutes = {
    update: (projectId: number, taskId: number, id: CommentID) => `/projects/${projectId}/tasks/${taskId}/comments/${id}`,
    destroy: (projectId: number, taskId: number, id: CommentID) => `/projects/${projectId}/tasks/${taskId}/comments/${id}`,
};