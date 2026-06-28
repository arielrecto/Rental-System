<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success');
const timeout = ref(null);

const trigger = (msg, t) => {
    message.value = msg;
    type.value = t;
    show.value = true;
    clearTimeout(timeout.value);
    timeout.value = setTimeout(() => { show.value = false; }, 5000);
};

watch(
    () => page.props.flash?.success,
    (val) => { if (val) trigger(val, 'success'); },
    { immediate: true }
);

watch(
    () => page.props.flash?.error,
    (val) => { if (val) trigger(val, 'error'); },
    { immediate: true }
);

onBeforeUnmount(() => clearTimeout(timeout.value));
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed top-4 right-4 z-50 max-w-sm w-full">
            <div
                class="rounded-lg shadow-lg p-4"
                :class="type === 'success' ? 'bg-green-50' : 'bg-red-50'"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- Success icon -->
                        <svg v-if="type === 'success'" class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <!-- Error icon -->
                        <svg v-else class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium" :class="type === 'success' ? 'text-green-800' : 'text-red-800'">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <button
                            @click="show = false"
                            class="inline-flex rounded-md p-1.5 focus:outline-none"
                            :class="type === 'success' ? 'text-green-500 hover:bg-green-100' : 'text-red-500 hover:bg-red-100'"
                        >
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
