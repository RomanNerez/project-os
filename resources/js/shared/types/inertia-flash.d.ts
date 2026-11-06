declare module '@inertiajs/core' {
    export interface InertiaConfig {
        flashDataType: {
            status?: string;
            created_task_id?: number;
        };
    }
}

export {};
