declare module '@inertiajs/core' {
    export interface InertiaConfig {
        /** One-time messages set on the server via Inertia::flash(). */
        flashDataType: {
            status?: string;
        };
    }
}

export {};
