import { computed, onBeforeUnmount, ref, watch, type ComputedRef, type Ref } from 'vue';

/**
 * Seconds elapsed since `startedAt`, ticking once per second while it is set.
 * `serverTime` compensates for a client clock that disagrees with the server,
 * so the running counter matches the duration the server will persist.
 */
export function useElapsedSeconds(
    startedAt: Ref<string | null>,
    serverTime: Ref<string>,
): ComputedRef<number> {
    const now = ref(Date.now());
    const clockOffset = ref(0);
    let timer: ReturnType<typeof setInterval> | null = null;

    watch(serverTime, (value) => {
        clockOffset.value = new Date(value).getTime() - Date.now();
    }, { immediate: true });

    function stopTicking(): void {
        if (timer === null) return;

        clearInterval(timer);
        timer = null;
    }

    watch(startedAt, (value) => {
        stopTicking();
        now.value = Date.now();

        if (!value) return;

        timer = setInterval(() => {
            now.value = Date.now();
        }, 1000);
    }, { immediate: true });

    onBeforeUnmount(stopTicking);

    return computed(() => {
        if (!startedAt.value) return 0;

        const started = new Date(startedAt.value).getTime();

        return Math.max(0, Math.floor((now.value + clockOffset.value - started) / 1000));
    });
}
