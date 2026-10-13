import type { CommentIncludes } from "@/entities/comment";
import type { Media } from "@/entities/media";
import type { TaskAssignee, TaskIncludes } from "@/entities/task";
import type { User } from "@/entities/user";

export type ProjectTaskProp = TaskIncludes<
    Media[],
    null,
    TaskAssignee | null,
    CommentIncludes<User>[]
>;
