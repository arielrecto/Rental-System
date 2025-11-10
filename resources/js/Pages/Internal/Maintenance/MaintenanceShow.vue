<script setup>
import { computed } from "vue";
import InternalLayout from "@/Layouts/InternalLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    CalendarIcon,
    CurrencyDollarIcon,
    ClipboardDocumentCheckIcon,
    TruckIcon,
} from "@heroicons/vue/24/outline";

const props = defineProps({
    request: Object,
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("en-PH", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>

<template>
    <InternalLayout>
        <Head title="Maintenance Request Details" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Maintenance Request Details
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Reference: {{ request.ref_number }}
                        </p>
                    </div>
                    <div class="flex space-x-3">

                        <!-- <Link
                            :href="
                                route('internal.maintenance.mark-as-on-going', request.id)
                            "
                            class="inline-flex items-center px-4 py-2 bg-blue-500 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-600  "
                        >
                            On Going
                        </Link> -->
                        <Link
                            :href="
                                route('internal.maintenance.mark-as-completed', request.id)
                            "
                            class="inline-flex items-center px-4 py-2 bg-green-500 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-600  "
                        >
                            Done
                        </Link>
                        <Link
                            :href="
                                route('internal.maintenance.edit', request.id)
                            "
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Edit Request
                        </Link>
                        <Link
                            :href="route('internal.maintenance.index')"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700"
                        >
                            Back to List
                        </Link>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <!-- Status Badge -->
                        <div class="mb-6">
                            <span
                                :class="[
                                    'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                                    request.is_completed
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-yellow-100 text-yellow-800',
                                ]"
                            >
                                {{ request.status }}
                            </span>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Vehicle Details -->
                            <div class="flex items-start space-x-3">
                                <TruckIcon class="h-6 w-6 text-gray-400" />
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        Vehicle
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ request.vehicle.name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Plate: {{ request.vehicle.plate }}
                                    </p>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="flex items-start space-x-3">
                                <CalendarIcon class="h-6 w-6 text-gray-400" />
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        Dates
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Requested:
                                        {{ formatDate(request.requested_date) }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Maintenance:
                                        {{
                                            formatDate(
                                                request.maintainance_date
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Cost -->
                            <div class="flex items-start space-x-3">
                                <CurrencyDollarIcon
                                    class="h-6 w-6 text-gray-400"
                                />
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        Cost
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ formatCurrency(request.cost) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <div class="flex items-start space-x-3">
                                <ClipboardDocumentCheckIcon
                                    class="h-6 w-6 text-gray-400"
                                />
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        Description
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ request.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
