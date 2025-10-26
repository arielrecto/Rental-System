<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import KioskLayout from "@/Layouts/KioskLayout.vue";
import LeafletMap from "@/Components/LeafletMap.vue";
import { Head } from "@inertiajs/vue3";
import { router, Link } from "@inertiajs/vue3";

const props = defineProps({
    session: Object,
    sessionId: String,
    rentalOrderId: String,
});

const computeDuration = (startTime) => {
    const start = new Date(startTime);
    const now = new Date();
    const diff = Math.floor((now - start) / 1000 / 60); // minutes
    const hours = Math.floor(diff / 60);
    const minutes = diff % 60;
    return `${hours}h ${minutes}m`;
};



const showSessionsDropdown = ref(false);


const activeSession = ref({
    id: props.session.id,
    lat: props.session.latest_location?.lat ?? 14.5995,
    lng: props.session.latest_location?.lng ?? 120.9842,
    title: `${props.session.vehicle.model} - ${props.session.customer.name}`,
    sessionToken : props.session.session_token,
    status: props.session.status,
    startTime: new Date(props.session.started_at).toLocaleTimeString(),
    duration: computeDuration(props.session.started_at),
    vehicleDetails: {
        name: ` ${props.session.vehicle.model}`,
        model: props.session.vehicle.model,
        plate: props.session.vehicle.plate,
    },
    customer: {
        name: props.session.customer.name,
        phone: props.session.customer.phone,
    },
});

let locationUpdateInterval;

// Get current geolocation
const getCurrentLocation = () => {
    // 1. Check if geolocation is available
    if (!("geolocation" in navigator)) {
        console.error("Geolocation is not available in this browser.");
        // You might want to show an error message to the user here
        return;
    }

    console.log("Getting current location...");

    // 2. Call getCurrentPosition ONCE
    navigator.geolocation.getCurrentPosition(
        // --- SUCCESS CALLBACK ---
        // This runs if the user allows permission and location is found
        (position) => {
            console.log("Current position:", position);
            const { latitude, longitude } = position.coords;

            // 3. Send the data to your server
            axios.post(
                route("kiosk.session.update-location", props.sessionId),
                {
                    latitude,
                    longitude,
                    speed: position.coords.speed || 0,
                    direction: position.coords.heading || 0,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (page) => {
                        // Update local state
                        activeSession.value = {
                            ...activeSession.value,
                            lat: latitude,
                            lng: longitude,
                        };
                    },
                    onError: (errors) => {
                        console.error(
                            "Failed to update location (Inertia error):",
                            errors
                        );
                    },
                }
            );
        },

        // --- ERROR CALLBACK (CRITICAL) ---
        // This runs if the user denies permission or location fails
        (error) => {
            console.error("Error getting location:", error.message);
            // You can show a specific message based on the error code
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    console.error("User denied the request for Geolocation.");
                    // Inform the user they need to enable location services
                    break;
                case error.POSITION_UNAVAILABLE:
                    console.error("Location information is unavailable.");
                    break;
                case error.TIMEOUT:
                    console.error(
                        "The request to get user location timed out."
                    );
                    break;
                case error.UNKNOWN_ERROR:
                    console.error("An unknown error occurred.");
                    break;
            }
        },

        // --- OPTIONS (Optional but recommended) ---
        {
            enableHighAccuracy: true, // Request the most accurate position
            timeout: 10000, // 10-second timeout
            maximumAge: 0, // Don't use a cached position
        }
    );
};

// Update duration every minute
const updateDuration = () => {
    activeSession.value.duration = computeDuration(props.session.started_at);
};


const formatDuration = (startTime) => {
    const start = new Date(startTime);
    const now = new Date();
    const diff = Math.floor((now - start) / 1000 / 60); // minutes
    const hours = Math.floor(diff / 60);
    const minutes = diff % 60;
    return `${hours}h ${minutes}m`;
};

onMounted(() => {
    // Update location every 5 seconds
    locationUpdateInterval = setInterval(getCurrentLocation, 5000);

    // Update duration every minute
    setInterval(updateDuration, 60000);

    // Initial location update
    getCurrentLocation();
});

onUnmounted(() => {
    if (locationUpdateInterval) {
        clearInterval(locationUpdateInterval);
    }
});
</script>

<template>
    <KioskLayout>
        <template #header-right>
            <!-- Active Sessions Dropdown -->
            <div class="relative">

                 <button
                    @click="showSessionsDropdown = !showSessionsDropdown"
                    class="flex items-center px-4 py-2 space-x-2 text-red-700 bg-red-50 rounded-lg hover:bg-red-100"
                >
                    <span class="font-medium">
                        Active Sessions
                    </span>
                    <ChevronDownIcon class="w-5 h-5" />
                </button>

                <!-- Dropdown Panel -->
                <div
                    v-if="showSessionsDropdown"
                    class="absolute right-0 z-50 py-2 mt-2 w-80 bg-white rounded-lg shadow-lg"
                >

                        <div

                            class="p-3 hover:bg-gray-50"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ activeSession.vehicleDetails.name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ activeSession.vehicleDetails.model }}
                                    </p>
                                </div>
                                <Link :href="route('kiosk.session.close', activeSession.sessionToken)" method="POST">
                                    Close Session
                                </Link>
                            </div>
                        </div>

                </div>
            </div>
        </template>
        <Head title="Active Session Details" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Active Session Details
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        View and manage the active session
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Session Details -->
                    <div class="lg:col-span-1 space-y-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">
                                Session Details
                            </h2>
                            <div class="space-y-3">
                                <div class="p-3 rounded-lg bg-gray-50">
                                    <div
                                        class="flex justify-between items-start"
                                    >
                                        <div>
                                            <h3
                                                class="font-medium text-gray-900"
                                            >
                                                {{
                                                    activeSession.vehicleDetails
                                                        .model
                                                }}
                                            </h3>
                                            <p class="text-sm text-gray-500">
                                                {{
                                                    activeSession.customer.name
                                                }}
                                            </p>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-red-600"
                                        >
                                            {{ activeSession.duration }}
                                        </span>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        Started at {{ activeSession.startTime }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="lg:col-span-3 space-y-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <LeafletMap
                                :locations="[activeSession]"
                                class="h-[600px]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </KioskLayout>
</template>
