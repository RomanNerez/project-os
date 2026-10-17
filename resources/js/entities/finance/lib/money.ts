import { FINANCE_CURRENCY, CURRENCY_META, type FinanceCurrency } from '../model/types';

/** Rounds to kopiykas; `toPrecision` drops float noise so x.xx5 rounds up like PHP's `round()`. */
function roundMoney(value: number): number {
    return Math.round(Number((value * 100).toPrecision(15))) / 100;
}

/**
 * Same formula as the server (CreateFinanceAction): UAH as is, USD × rate.
 * `null` while the amount (or the USD rate) is not entered yet.
 */
export function calcAmountUah(
    amount: number | null,
    currency: FinanceCurrency,
    rate: number | null,
): number | null {
    if (amount === null) return null;

    if (currency === FINANCE_CURRENCY.UAH) return roundMoney(amount);

    return rate ? roundMoney(amount * rate) : null;
}

const formatters = new Map<FinanceCurrency, Intl.NumberFormat>();

export function formatMoney(value: number | string | null, currency: FinanceCurrency): string {
    if (value === null || value === '') return '—';

    let formatter = formatters.get(currency);

    if (!formatter) {
        formatter = new Intl.NumberFormat('uk-UA', { style: 'currency', currency: CURRENCY_META[currency].code });
        formatters.set(currency, formatter);
    }

    return formatter.format(Number(value));
}
