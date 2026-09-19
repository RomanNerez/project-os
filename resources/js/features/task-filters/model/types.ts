import type { TaskStatus } from "@/entities/task";

export interface TaskFilters {
    title: string;
    status: TaskStatus[]
}