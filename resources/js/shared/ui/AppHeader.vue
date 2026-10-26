<template>
    <header class="flex h-12 shrink-0 items-center gap-2 border-b border-surface-200 dark:border-surface-700 px-4">
        <slot name="start"></slot>
        <div class="ml-auto flex items-center gap-2">
            <slot></slot>
        </div>
    </header>
</template>
