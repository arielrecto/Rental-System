<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import InternalLayout from "@/Layouts/InternalLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    UserIcon,
    TruckIcon,
    CalendarIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
    DeviceTabletIcon
} from "@heroicons/vue/24/outline";

const props = defineProps({
    rentalOrder: {
        type: Object,
        required: true,
    },
});

const getStatusColor = (status) => {
    const colors = {
        pending: "bg-yellow-100 text-yellow-800",
        active: "bg-green-100 text-green-800",
        completed: "bg-blue-100 text-blue-800",
        cancelled: "bg-red-100 text-red-800",
    };
    return colors[status] || "bg-gray-100 text-gray-800";
};

const calculateDuration = () => {
    const start = new Date(props.rentalOrder.start_date);
    const end = new Date(props.rentalOrder.end_date);
    return Math.ceil((end - start) / (1000 * 60 * 60 * 24));
};

console.log(props.rentalOrder);
</script>

<template>
    <InternalLayout>
        <Head :title="`Rental Order #${rentalOrder.id}`" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Rental Order #{{ rentalOrder.id }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Created on
                            {{
                                new Date(
                                    rentalOrder.created_at
                                ).toLocaleDateString()
                            }}
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="
                                route(
                                    'internal.rental-orders.edit',
                                    rentalOrder.id
                                )
                            "
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-red-300 focus:ring focus:ring-red-200 active:text-gray-800 active:bg-gray-50"
                        >
                            Edit Order
                        </Link>
                        <button
                            v-if="rentalOrder.status === 'pending'"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700"
                        >
                            Approve Order
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Status Card -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">
                            Order Status
                        </h2>
                        <span
                            :class="[
                                getStatusColor(rentalOrder.status),
                                'px-3 py-1 rounded-full text-sm font-semibold',
                            ]"
                        >
                            {{
                                rentalOrder.status.charAt(0).toUpperCase() +
                                rentalOrder.status.slice(1)
                            }}
                        </span>
                    </div>

                    <!-- Customer Details -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <UserIcon class="h-5 w-5 text-gray-400 mr-2" />
                            <h2 class="text-lg font-medium text-gray-900">
                                Customer Details
                            </h2>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-900">
                                {{ rentalOrder.user.name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ rentalOrder.user.email }}
                            </p>
                            <p
                                v-if="rentalOrder.user.profile?.phone"
                                class="text-sm text-gray-600"
                            >
                                {{ rentalOrder.user.profile.phone }}
                            </p>
                        </div>
                    </div>

                    <!-- Vehicle Details -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <TruckIcon class="h-5 w-5 text-gray-400 mr-2" />
                            <h2 class="text-lg font-medium text-gray-900">
                                Vehicle Details
                            </h2>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-900">
                                {{ rentalOrder.vehicle.brand }}
                                {{ rentalOrder.vehicle.model }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Plate: {{ rentalOrder.vehicle.plate_no }}
                            </p>
                            <p class="text-sm font-semibold text-red-600">
                                ₱{{
                                    rentalOrder.vehicle.rental_rate.toLocaleString()
                                }}/day
                            </p>
                        </div>
                    </div>

                    <!-- Rental Period -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <CalendarIcon class="h-5 w-5 text-gray-400 mr-2" />
                            <h2 class="text-lg font-medium text-gray-900">
                                Rental Period
                            </h2>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600">
                                Start:
                                {{
                                    new Date(
                                        rentalOrder.start_date
                                    ).toLocaleDateString()
                                }}
                            </p>
                            <p class="text-sm text-gray-600">
                                End:
                                {{
                                    new Date(
                                        rentalOrder.end_date
                                    ).toLocaleDateString()
                                }}
                            </p>
                            <p class="text-sm font-medium text-gray-900">
                                Duration: {{ calculateDuration() }} days
                            </p>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <CurrencyDollarIcon
                                class="h-5 w-5 text-gray-400 mr-2"
                            />
                            <h2 class="text-lg font-medium text-gray-900">
                                Payment Details
                            </h2>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600"
                                    >Daily Rate</span
                                >
                                <span class="text-sm text-gray-900"
                                    >₱{{
                                        rentalOrder.vehicle.rental_rate.toLocaleString()
                                    }}</span
                                >
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600"
                                    >Duration</span
                                >
                                <span class="text-sm text-gray-900"
                                    >{{ calculateDuration() }} days</span
                                >
                            </div>
                            <div class="flex justify-between pt-2 border-t">
                                <span class="text-sm font-medium text-gray-900"
                                    >Total Amount</span
                                >
                                <span class="text-sm font-bold text-red-600"
                                    >₱{{
                                        rentalOrder.total_amount.toLocaleString()
                                    }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded-lg p-6">
                        <template
                            v-if="
                                rentalOrder.rental_vehicle_sessions?.length > 0
                            "
                        >
                            <div class="flex items-center mb-4 justify-between">

                                <div class="flex items-center gap-2">
                                <DeviceTabletIcon
                                    class="h-5 w-5 text-gray-400 mr-2"
                                />
                                <h2 class="text-lg font-medium text-gray-900">
                                    Rental Session Details
                                </h2>
                                </div>
                               
                                <h1 class="text-xs text-green-500 font-bold">Status : {{ rentalOrder.rental_vehicle_sessions[0].status }}</h1>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >Start</span
                                    >
                                    <span class="text-sm text-gray-900">{{
                                        rentalOrder.rental_vehicle_sessions[0]
                                            .started_at
                                    }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600"
                                        >End</span
                                    >
                                    <span class="text-sm text-gray-900">{{
                                        rentalOrder.rental_vehicle_sessions[0]
                                            .ended_at
                                    }}</span>
                                </div>

                                <a class="text-blue-500 text-xs underline" target="_blank"
                                    :href="
                                        route('kiosk.session', {
                                            sessionId:
                                                rentalOrder
                                                    .rental_vehicle_sessions[0]
                                                    .session_token,
                                            rentalOrderId: rentalOrder.id,
                                        })
                                    "
                                    >View Session</a>
                                <div class="flex justify-between pt-2 border-t">
                                    <span
                                        class="text-sm font-medium text-gray-900"
                                        >Total Amount</span
                                    >
                                    <span class="text-sm font-bold text-red-600"
                                        >₱{{
                                            rentalOrder.total_amount.toLocaleString()
                                        }}</span
                                    >
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex items-center gap-2 justify-center">
                                <p class="text-gray-500">
                                    No rental session data available.
                                </p>
                            </div>
                        </template>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center mb-4">
                            <DocumentTextIcon
                                class="h-5 w-5 text-gray-400 mr-2"
                            />
                            <h2 class="text-lg font-medium text-gray-900">
                                Notes
                            </h2>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ rentalOrder.notes || "No notes provided" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
