import { FINANCE_CURRENCY, type FinanceCurrency, type FinanceTaskHours, type FinanceTaskHoursUser, type FinanceTaskPayment, type FinanceTaskUserHours } from '../model/types';
import { roundMoney } from './money';

export const PAYMENT_STEP_SECONDS = 1;

export function unpaidSeconds(hours: FinanceTaskUserHours): number {
    return Math.max(0, hours.tracked_seconds - hours.paid_seconds);
}

export function stepsFor(max: number): number {
    return Math.ceil(max / PAYMENT_STEP_SECONDS);
}

export function secondsToSteps(seconds: number, max: number): number {
    return seconds >= max ? stepsFor(max) : Math.round(seconds / PAYMENT_STEP_SECONDS);
}

export function stepsToSeconds(steps: number, max: number): number {
    return Math.min(steps * PAYMENT_STEP_SECONDS, max);
}

export function calcPaymentsUsd(payments: FinanceTaskPayment[], hoursByTask: Map<number, FinanceTaskHours>): number {
    return payments.reduce((sum, payment) => {
        const rate = hoursByTask.get(payment.task_id)?.users
            .find((hours) => hours.user.id === payment.user_id)?.hourly_rate ?? 0;

        return sum + (payment.seconds / 3600) * rate;
    }, 0);
}

export function calcMinAmount(usd: number, currency: FinanceCurrency, exchangeRate: number | null): number | null {
    if (currency === FINANCE_CURRENCY.USD) return roundMoney(usd);

    return exchangeRate ? roundMoney(usd * exchangeRate) : null;
}

export function paymentsOfTask(payments: FinanceTaskPayment[], taskId: number): Record<number, number> {
    return Object.fromEntries(payments
        .filter((payment) => payment.task_id === taskId)
        .map((payment) => [payment.user_id, payment.seconds]));
}

export function withTaskPayments(
    payments: FinanceTaskPayment[],
    taskId: number,
    byUser: Record<number, number>,
): FinanceTaskPayment[] {
    return [
        ...payments.filter((payment) => payment.task_id !== taskId),
        ...Object.entries(byUser).map(([userId, seconds]) => ({ task_id: taskId, user_id: Number(userId), seconds })),
    ];
}

/** Users of a task as `FinanceTaskHoursCard` shows them: what is still unpaid is what can be paid now. */
export function toHoursUsers(users: FinanceTaskUserHours[]): FinanceTaskHoursUser[] {
    return users.map((hours) => ({ ...hours.user, seconds: unpaidSeconds(hours) }));
}
