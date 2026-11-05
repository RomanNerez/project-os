import type { FinanceTaskUserPayment } from "@/entities/finance";
import type { Project } from "@/entities/project";
import type { TaskIncludes } from "@/entities/task";
import type { TimeEntryIncludes } from "@/entities/time-entry";
import type { User } from "@/entities/user";

export type TaskOption = TaskIncludes<null, Project, null, null, TimeEntryIncludes<User>[], null, FinanceTaskUserPayment[]>
