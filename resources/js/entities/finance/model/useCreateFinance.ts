import { useForm } from '@inertiajs/vue3';
import { toDateValue } from '@/shared/lib';
import { emptyFinanceDraft, FINANCE_CURRENCY, type FinanceDraft } from './types';
import { financeRoutes } from '../api/financeRoutes';

export function useCreateFinance(onDone: () => void) {
    const form = useForm<FinanceDraft>(emptyFinanceDraft());

    function reset(): void {
        form.defaults(emptyFinanceDraft());
        form.reset();
        form.clearErrors();
    }

    function submit(): void {
        if (!form.task_ids.length) {
            form.setError('task_ids', 'Оберіть хоча б одну задачу');
            return;
        }

        // Inertia sends the payload as FormData when `receipts` holds files.
        form
            .transform((data) => ({
                ...data,
                exchange_rate: data.currency === FINANCE_CURRENCY.USD ? data.exchange_rate : null,
                due_date: toDateValue(data.due_date),
                paid_at: toDateValue(data.paid_at),
            }))
            .post(financeRoutes.store(), {
                onSuccess: onDone,
                preserveScroll: true,
                reset: ['finances'],
            });
    }

    return { form, submit, reset };
}
