<script setup>
import { onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg, xl, full
        validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value)
    },
    maxHeight: {
        type: String,
        default: '90vh'
    },
    closeOnClickOutside: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue', 'close']);

const close = () => {
    emit('update:modelValue', false);
    emit('close');
};

const handleEscape = (e) => {
    if (e.key === 'Escape' && props.modelValue) {
        close();
    }
};

const preventScroll = () => {
    document.body.style.overflow = 'hidden';
};

const enableScroll = () => {
    document.body.style.overflow = '';
};

onMounted(() => {
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleEscape);
    enableScroll();
});

watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        preventScroll();
    } else {
        enableScroll();
    }
});

const modalSizes = {
    sm: 'max-w-md',
    md: 'max-w-lg',
    lg: 'max-w-2xl',
    xl: 'max-w-4xl',
    full: 'max-w-full mx-4'
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="modelValue"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                @click="closeOnClickOutside && close()"
            ></div>

            <!-- Modal -->
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full"
                    :class="modalSizes[size]"
                    @click.stop
                    :style="{ 'max-height': maxHeight }"
                >
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200" v-if="$slots.header">
                        <div class="flex items-center justify-between">
                            <div class="text-lg font-medium text-gray-900">
                                <slot name="header"></slot>
                            </div>
                            <button
                                @click="close"
                                class="text-gray-400 hover:text-red-500 focus:outline-none"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-4 overflow-y-auto" :style="{ 'max-height': `calc(${maxHeight} - 10rem)` }">
                        <slot></slot>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200" v-if="$slots.footer">
                        <slot name="footer"></slot>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
