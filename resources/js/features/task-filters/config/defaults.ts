import type { TaskFilters } from "../model/types";

export const emptyTaskFilters = (): TaskFilters => ({
    title: '',
    status: [],
});