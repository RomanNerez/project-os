import type { IncludedData } from '@/shared/types';

export const FINANCE_TYPE = {
    INCOME: 'income',
    EXPENSE: 'expense',
} as const;

export const FINANCE_CURRENCY = {
    UAH: 'uah',
    USD: 'usd',
} as const;

export const FINANCE_STATUS = {
    PLANNED: 'planned',
    PAID: 'paid',
    PENDING: 'pending',
    OVERDUE: 'overdue',
    CANCELLED: 'cancelled',
} as const;

export type FinanceType = typeof FINANCE_TYPE[keyof typeof FINANCE_TYPE];
export type FinanceCurrency = typeof FINANCE_CURRENCY[keyof typeof FINANCE_CURRENCY];
export type FinanceStatus = typeof FINANCE_STATUS[keyof typeof FINANCE_STATUS];
export type FinanceID = number;

type Severity = 'secondary' | 'info' | 'warn' | 'success' | 'danger';

export const STATUS_META: Record<FinanceStatus, { label: string; severity: Severity }> = {
    planned: { label: 'Заплановано', severity: 'info' },
    paid: { label: 'Оплачено', severity: 'success' },
    pending: { label: 'Очікує', severity: 'warn' },
    overdue: { label: 'Прострочено', severity: 'danger' },
    cancelled: { label: 'Скасовано', severity: 'secondary' },
};

export const TYPE_META: Record<FinanceType, { label: string; severity: Severity }> = {
    income: { label: 'Дохід', severity: 'success' },
    expense: { label: 'Витрата', severity: 'danger' },
};

export const CURRENCY_META: Record<FinanceCurrency, { label: string; code: string }> = {
    uah: { label: '₴ UAH', code: 'UAH' },
    usd: { label: '$ USD', code: 'USD' },
};

export const STATUS_OPTIONS = (Object.keys(STATUS_META) as FinanceStatus[])
    .map((value) => ({ value, label: STATUS_META[value].label }));

export const TYPE_OPTIONS = (Object.keys(TYPE_META) as FinanceType[])
    .map((value) => ({ value, label: TYPE_META[value].label }));

export const CURRENCY_OPTIONS = (Object.keys(CURRENCY_META) as FinanceCurrency[])
    .map((value) => ({ value, label: CURRENCY_META[value].label }));

export interface FinanceProject {
    id: number;
    title: string;
}

export interface FinanceTask {
    id: number;
    title: string;
    project_id: number;
}

/** Money comes as decimal strings so no precision is lost. */
export interface Finance {
    id: FinanceID;
    title: string;
    description: string | null;
    type: FinanceType;
    currency: FinanceCurrency;
    amount: string;
    exchange_rate: string | null;
    amount_uah: string;
    status: FinanceStatus;
    due_date: string | null;
    paid_at: string | null;
    created_at: string | null;
    projects: FinanceProject[];
}

export interface FinanceIncludes<TTasks = null, TMedia = null> extends Finance {
    tasks: IncludedData<TTasks>;
    media: IncludedData<TMedia>;
}

/** A receipt as FinanceCard shows it. */
export interface FinanceCardFile {
    id: number;
    name: string;
    url: string;
    isPdf: boolean;
}

export interface FinanceDraft {
    title: string;
    type: FinanceType;
    currency: FinanceCurrency;
    amount: number | null;
    exchange_rate: number | null;
    status: FinanceStatus;
    due_date: Date | null;
    paid_at: Date | null;
    description: string;
    task_ids: number[];
    receipts: File[];
}

export const emptyFinanceDraft = (): FinanceDraft => ({
    title: '',
    type: FINANCE_TYPE.EXPENSE,
    currency: FINANCE_CURRENCY.UAH,
    amount: null,
    exchange_rate: null,
    status: FINANCE_STATUS.PLANNED,
    due_date: null,
    paid_at: null,
    description: '',
    task_ids: [],
    receipts: [],
});
