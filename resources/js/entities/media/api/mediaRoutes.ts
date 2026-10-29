import type { MediaID } from "../model/types";

export const mediaRoutes = {
    destroy: (projectId: number, taskId: number, id: MediaID) => `/projects/${projectId}/tasks/${taskId}/media/${id}`,
};