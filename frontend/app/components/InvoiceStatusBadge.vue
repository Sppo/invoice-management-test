<template>
    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="config.classes">
        <span class="h-1.5 w-1.5 rounded-full" :class="config.dot" />
        {{ config.label }}
    </span>
</template>

<script setup lang="ts">
import type { InvoiceStatus } from '~/types/invoice';

const props = defineProps<{
    status: InvoiceStatus;
}>();

const STATUS_CONFIG: Record<InvoiceStatus, { label: string; classes: string; dot: string }> = {
    pending: {
        label: 'В очікуванні',
        classes: 'bg-amber-100 text-amber-800',
        dot: 'bg-amber-500',
    },
    approved: {
        label: 'Підтверджено',
        classes: 'bg-emerald-100 text-emerald-800',
        dot: 'bg-emerald-500',
    },
    rejected: {
        label: 'Відхилено',
        classes: 'bg-rose-100 text-rose-800',
        dot: 'bg-rose-500',
    },
};

function getConfig() {
    return STATUS_CONFIG[props.status];
}

const config = computed(getConfig);
</script>