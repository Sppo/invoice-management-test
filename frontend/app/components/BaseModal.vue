<template>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                @click.self="close">
                <div class="w-full max-w-lg rounded-lg bg-white shadow-xl">
                    <div v-if="title" class="flex items-center justify-between border-b px-5 py-3">
                        <h2 class="text-lg font-semibold">{{ title }}</h2>
                        <button type="button" class="text-gray-500 hover:text-gray-800 cursor-pointer"
                            aria-label="Закрити" @click="close">
                            ✕
                        </button>
                    </div>
                    <div class="px-5 py-4">
                        <slot />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">

const props = defineProps<{
    modelValue: boolean;
    title?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
}>();

function close() {
    emit('update:modelValue', false);
}


watch(
    () => props.modelValue,
    (open) => {
        if (import.meta.client) {
            document.body.style.overflow = open ? 'hidden' : '';
        }
    },
);


onBeforeUnmount(() => {

    if (import.meta.client) document.body.style.overflow = '';
});
</script>


<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>