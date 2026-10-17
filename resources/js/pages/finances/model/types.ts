import type { FinanceIncludes, FinanceTask } from '@/entities/finance';
import type { Media } from '@/entities/media';

export type FinanceProp = FinanceIncludes<FinanceTask[], Media[]>;
