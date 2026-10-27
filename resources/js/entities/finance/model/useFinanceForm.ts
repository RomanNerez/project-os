import { watch, type Ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { emptyFinanceDraft, FINANCE_CURRENCY, financeToDraft, type FinanceDraft, type FinanceWithTasks } from './types';
import { financeRoutes } from '../api/financeRoutes';

/** Creates a record when `finance` is null (in `projectId`, if given), otherwise edits it. */
export function useFinanceForm(finance: Ref<FinanceWithTasks | null>, onDone: () => void, projectId: number | null = null) {
    const form = useForm<FinanceDraft>(emptyFinanceDraft());

    function reset(): void {
        form.defaults(finance.value ? financeToDraft(finance.value) : { ...emptyFinanceDraft(), project_id: projectId });
        form.reset();
        form.clearErrors();
    }

    watch(finance, reset, { immediate: true });

    function submit(): void {
        if (!form.project_id) {
            form.setError('project_id', 'Оберіть проєкт');
            return;
        }

        const current = finance.value;

        // Inertia sends the payload as FormData when `receipts` holds files, and PHP only
        // parses multipart bodies of POST requests, so an update is a POST spoofing PUT.
        form
            .transform((data) => ({
                ...data,
                exchange_rate: data.currency === FINANCE_CURRENCY.USD ? data.exchange_rate : null,
                ...(current ? { _method: 'put' } : {}),
            }))
            .post(current ? financeRoutes.update(current.id) : financeRoutes.store(), {
                onSuccess: onDone,
                preserveScroll: true,
                reset: ['finances'],
            });
    }

    return { form, submit, reset };
}
