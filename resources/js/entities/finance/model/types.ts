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
    payments?: Omit<FinanceTaskUserPayment, 'task_id'>[];
}

/** One row of `task_payments` sent with the form: how many seconds of the user's time on the task are paid. */
export interface FinanceTaskPayment {
    task_id: number;
    user_id: number;
    seconds: number;
}

export interface FinanceTaskUserPayment {
    task_id: number;
    user_id: number;
    seconds: number;
    hourly_rate?: string | null;
}

export interface FinanceTaskUser {
    id: number;
    name: string;
    avatar_url: string | null;
}
export interface FinanceTaskHoursUser extends FinanceTaskUser {
    seconds: number;
}
export interface FinanceTaskHoursChange {
    user_id: number;
    seconds: number;
}

export interface FinanceTaskUserHours {
    user: FinanceTaskUser;
    tracked_seconds: number;
    paid_seconds: number;
    hourly_rate: number | null;
}

export interface FinanceTaskHours {
    task: FinanceTask;
    users: FinanceTaskUserHours[];
}
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
    project_id: number;
    permissions: FinancePermissions;
}

export interface FinancePermissions {
    update: boolean;
}

export interface FinanceIncludes<TProject = null, TTasks = null, TMedia = null> extends Finance {
    project: IncludedData<TProject>;
    tasks: IncludedData<TTasks>;
    media: IncludedData<TMedia>;
}
export interface FinanceCardFile {
    id: number;
    name: string;
    url: string;
    isPdf: boolean;
}

export type FinanceWithTasks = Finance & { tasks: IncludedData<FinanceTask[]> };
export interface FinanceDraft {
    title: string;
    type: FinanceType;
    currency: FinanceCurrency;
    amount: number | null;
    exchange_rate: number | null;
    status: FinanceStatus;
    due_date: string | null;
    paid_at: string | null;
    description: string;
    task_ids: number[];
    task_payments: FinanceTaskPayment[];
    receipts: File[];
    removed_receipt_ids: number[];
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
    task_payments: [],
    receipts: [],
    removed_receipt_ids: [],
});

export const financeToDraft = (finance: FinanceWithTasks): FinanceDraft => ({
    title: finance.title,
    type: finance.type,
    currency: finance.currency,
    amount: Number(finance.amount),
    exchange_rate: finance.exchange_rate === null ? null : Number(finance.exchange_rate),
    status: finance.status,
    due_date: finance.due_date,
    paid_at: finance.paid_at,
    description: finance.description ?? '',
    task_ids: finance.tasks.data.map((task) => task.id),
    task_payments: finance.tasks.data.flatMap((task) => (task.payments ?? [])
        .map((payment) => ({ task_id: task.id, ...payment }))),
    receipts: [],
    removed_receipt_ids: [],
});
