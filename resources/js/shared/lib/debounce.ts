export function debounce<TArgs extends unknown[]>(
    callback: (...args: TArgs) => void,
    delay: number,
): (...args: TArgs) => void {
    let timeout: ReturnType<typeof setTimeout> | undefined;

    return (...args: TArgs): void => {
        clearTimeout(timeout);
        timeout = setTimeout(() => callback(...args), delay);
    };
}
