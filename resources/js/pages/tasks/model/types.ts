import type { CommentIncludes } from "@/entities/comment";
import type { Media } from "@/entities/media";
import type { ProjectIncludes, ProjectMember } from "@/entities/project";
import type { TaskIncludes } from "@/entities/task";
import type { User } from "@/entities/user";

export type TaskProp = TaskIncludes<
    Media[],
    ProjectIncludes<User, ProjectMember[]>,
    User | null,
    CommentIncludes<User>[]
>;