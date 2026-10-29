import type { CommentIncludes } from "@/entities/comment";
import type { Media } from "@/entities/media";
import type { ProjectIncludes, ProjectMember } from "@/entities/project";
import type { TaskActivityIncludes, TaskIncludes } from "@/entities/task";
import type { TimeEntryIncludes } from "@/entities/time-entry";
import type { User } from "@/entities/user";

export type TaskProp = TaskIncludes<
    Media[],
    ProjectIncludes<User, ProjectMember[]>,
    User | null,
    CommentIncludes<User>[],
    TimeEntryIncludes<User>[],
    TaskActivityIncludes<User>[]
>;