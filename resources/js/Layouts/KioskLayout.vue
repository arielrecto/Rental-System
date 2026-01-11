<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { ChevronDownIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    activeSessions: Array,
    default() {
        [
            {
                id: 1,
                session_token: 'RS001',
                vehicle: { name: 'Ninja ZX-10R', plate_no: 'ABC 123' },
                customer: { name: 'John Doe' },
                started_at: '2023-10-25 09:30:00',
                status: 'active'
            },
            {
                id: 2,
                session_token: 'RS002',
                vehicle: { name: 'MT-07', plate_no: 'XYZ 789' },
                customer: { name: 'Jane Smith' },
                started_at: '2023-10-25 10:15:00',
                status: 'active'
            }
        ]
    }
})



const currentTime = ref(new Date().toLocaleTimeString());
const currentDate = ref(new Date().toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
}));



// Demo data
const activeSessions = ref(props.activeSessions);

const showSessionsDropdown = ref(false);

// Update clock every second
setInterval(() => {
    currentTime.value = new Date().toLocaleTimeString();
}, 1000);

const formatDuration = (startTime) => {
    const start = new Date(startTime);
    const now = new Date();
    const diff = Math.floor((now - start) / 1000 / 60); // minutes
    const hours = Math.floor(diff / 60);
    const minutes = diff % 60;
    return `${hours}h ${minutes}m`;
};

console.log(props.activeSessions)
</script>

<template>
    <div class="min-h-screen bg-gray-50">


        <Head title="Rental Kiosk | Anaia's Motorcycle Rental" />

        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <img src="/logo.png" alt="Anaia's Motorcycle Rental" class="mr-4 w-auto h-12" />
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                GPS
                            </h1>
                            <p class="text-sm text-gray-500">
                                Manage rental sessions
                            </p>
                        </div>
                    </div>

                      <div class="flex items-center space-x-6">


                          <slot name="header-right"/>

                        <!-- Clock -->
                        <div class="text-right">
                            <p class="text-2xl font-bold text-red-600">
                                {{ currentTime }}
                            </p>
                            <p class="text-sm text-gray-500">{{ currentDate }}</p>
                        </div>
                    </div>



                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
