<script setup>
import { computed, onMounted, ref, watch, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';

const show = ref(false);
const timeout = ref(null);

const page = usePage();

const flash = computed(() => {
    const success = page.props.flash?.success;
    const error = page.props.flash?.error;

    if (success) {
        return { type: 'success', message: success };
    }

    if (error) {
        return { type: 'error', message: error };
    }

    return null;
});

const icon = computed(() => {
    if (!flash.value) return null;

    return flash.value.type === 'success'
        ? {
            class: 'text-green-400',
            path: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z'
        }
        : {
            class: 'text-red-400',
            path: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'
        };
});

const backgroundColor = computed(() => {
    if (!flash.value) return '';

    return flash.value.type === 'success'
        ? 'bg-green-50'
        : 'bg-red-50';
});

const textColor = computed(() => {
    if (!flash.value) return '';

    return flash.value.type === 'success'
        ? 'text-green-800'
        : 'text-red-800';
});

watch(() => flash.value, (newValue) => {
    if (newValue) {
        show.value = true;
        if (timeout.value) clearTimeout(timeout.value);
        timeout.value = setTimeout(() => {
            show.value = false;
        }, 5000);
    }
});

onMounted(() => {
    if (flash.value) {
        show.value = true;
        timeout.value = setTimeout(() => {
            show.value = false;
        }, 5000);
    }
});

onBeforeUnmount(() => {
    if (timeout.value) clearTimeout(timeout.value);
});
</script>

<template>
    <div
        v-if="show && flash"
        class="fixed top-4 right-4 z-50 max-w-sm w-full"
    >
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="rounded-lg shadow-lg"
            >
                <div :class="[backgroundColor, 'p-4 rounded-lg shadow-xs']">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg
                                class="h-5 w-5"
                                :class="icon.class"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    :d="icon.path"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p
                                :class="[textColor, 'text-sm font-medium']"
                            >
                                {{ flash.message }}
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button
                                    @click="show = false"
                                    :class="[textColor, 'inline-flex rounded-md p-1.5 hover:bg-white hover:bg-opacity-20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600']"
                                >
                                    <span class="sr-only">Dismiss</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
