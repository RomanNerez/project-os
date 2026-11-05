import { useForm } from '@inertiajs/vue3';
import { emptyFinanceDraft, financeToDraft, type FinanceDraft, type FinanceWithTasks } from './types';
import { financeRoutes } from '../api/financeRoutes';

/** Only payments with time for a selected task are sent. */
function toPayload(data: FinanceDraft): FinanceDraft {
    return {
        ...data,
        task_payments: data.task_payments
            .filter((payment) => payment.seconds > 0 && data.task_ids.includes(payment.task_id)),
    };
}

export function useFinanceForm() {
    const form = useForm<FinanceDraft>(emptyFinanceDraft());

    function reset(finance: FinanceWithTasks | null): void {
        form.defaults(finance ? financeToDraft(finance) : emptyFinanceDraft());
        form.reset();
        form.clearErrors();
    }

    function create(projectId: number, onDone: () => void): void {
        form
            .transform(toPayload)
            .post(financeRoutes.store(projectId), {
                onSuccess: onDone,
                preserveScroll: true,
                reset: ['finances'],
            });
    }

    // Sent as a spoofed PUT: receipts make it multipart.
    function update(projectId: number, financeId: number, onDone: () => void): void {
        form
            .transform((data) => ({ ...toPayload(data), _method: 'put' }))
            .post(financeRoutes.update(projectId, financeId), {
                onSuccess: onDone,
                preserveScroll: true,
                reset: ['finances'],
            });
    }

    return { form, reset, create, update };
}
