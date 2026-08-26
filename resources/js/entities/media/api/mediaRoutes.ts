import type { MediaID } from "../model/types";

export const mediaRoutes = {
    destroy: (id: MediaID) => `/media/${id}`,
};
