import type { FinanceIncludes, FinanceTask } from '@/entities/finance';
import type { Media } from '@/entities/media';
import type { Project } from '@/entities/project';

export type FinanceProp = FinanceIncludes<Project, FinanceTask[], Media[]>;
