import type { Media } from "@/entities/media";
import type { Project } from "@/entities/project";
import type { TaskIncludes } from "@/entities/task";
import type { User } from "@/entities/user";

export type TaskProp = TaskIncludes<Media[], Project, User>;