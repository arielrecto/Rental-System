<script setup>
import { onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        required: true
    },
    position: {
        type: String,
        default: 'right',
        validator: (value) => ['left', 'right', 'top', 'bottom'].includes(value)
    },
    width: {
        type: String,
        default: '400px'
    },
    height: {
        type: String,
        default: '100vh'
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

const getInitialTransform = computed(() => {
    switch (props.position) {
        case 'left': return 'translateX(-100%)';
        case 'right': return 'translateX(100%)';
        case 'top': return 'translateY(-100%)';
        case 'bottom': return 'translateY(100%)';
    }
});

const getTransitionClasses = computed(() => {
    const position = props.position;
    return {
        enter: `transform transition-transform ease-in-out duration-300`,
        enterFrom: position === 'left' ? '-translate-x-full' :
                  position === 'right' ? 'translate-x-full' :
                  position === 'top' ? '-translate-y-full' :
                  'translate-y-full',
        enterTo: 'translate-x-0 translate-y-0',
        leave: 'transform transition-transform ease-in-out duration-300',
        leaveFrom: 'translate-x-0 translate-y-0',
        leaveTo: position === 'left' ? '-translate-x-full' :
                position === 'right' ? 'translate-x-full' :
                position === 'top' ? '-translate-y-full' :
                'translate-y-full'
    };
});
</script>

<template>
    <div>
        <!-- Backdrop -->
        <Transition
            enter-active-class="transition-opacity ease-in-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-in-out duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="modelValue"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 z-40"
                @click="closeOnClickOutside && close()"
            />
        </Transition>

        <!-- Drawer -->
        <Transition
            :enter-active-class="getTransitionClasses.enter"
            :enter-from-class="getTransitionClasses.enterFrom"
            :enter-to-class="getTransitionClasses.enterTo"
            :leave-active-class="getTransitionClasses.leave"
            :leave-from-class="getTransitionClasses.leaveFrom"
            :leave-to-class="getTransitionClasses.leaveTo"
        >
            <div
                v-if="modelValue"
                class="fixed bg-white shadow-xl z-50 overflow-y-auto"
                :style="{
                    width: ['left', 'right'].includes(position) ? width : '100%',
                    height: ['top', 'bottom'].includes(position) ? height : '100%',
                    top: ['top', 'right', 'left'].includes(position) ? 0 : 'auto',
                    bottom: position === 'bottom' ? 0 : 'auto',
                    left: ['left', 'top', 'bottom'].includes(position) ? 0 : 'auto',
                    right: position === 'right' ? 0 : 'auto',
                }"
            >
                <!-- Header with close button -->
                <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                    <slot name="header">
                        <h2 class="text-lg font-medium text-gray-900">
                            <slot name="title"></slot>
                        </h2>
                    </slot>
                    <button
                        @click="close"
                        class="rounded-md text-gray-400 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                        <span class="sr-only">Close panel</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <slot></slot>
                </div>

                <!-- Footer -->
                <div v-if="$slots.footer" class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4">
                    <slot name="footer"></slot>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.fixed {
    position: fixed;
}
</style>
