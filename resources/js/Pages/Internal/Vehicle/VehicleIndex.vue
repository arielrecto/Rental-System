<script setup>
import { ref, computed } from "vue";
import InternalLayout from "@/Layouts/InternalLayout.vue";
import DataTable from "@/Components/DataTable.vue";
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import { router } from "@inertiajs/vue3";
import {
    ViewColumnsIcon,
    TableCellsIcon,
    PlusIcon,
} from "@heroicons/vue/24/outline";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    vehicles: {
        type: Object,
        required: true,
    },
});

const viewMode = ref("table"); // 'table' or 'card'
const searchQuery = ref("");

const headers = [
    { key: "id", label: "ID" },
    { key: "brand", label: "Brand" },
    { key: "model", label: "Model" },
    { key: "plate_number", label: "Plate Number" },
    { key: "rental_rate", label: "Daily Rate" },
    { key: "status", label: "Status" },
];

const filteredVehicles = computed(() => {
    return props.vehicles.data.filter((vehicle) =>
        Object.values(vehicle).some((value) =>
            String(value)
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase())
        )
    );
});

const getStatusColor = (status) => {
    const colors = {
        Available: "bg-green-100 text-green-800",
        Rented: "bg-blue-100 text-blue-800",
        Maintenance: "bg-yellow-100 text-yellow-800",
        Reserved: "bg-purple-100 text-purple-800",
    };
    return colors[status] || "bg-gray-100 text-gray-800";
};

const changePage = (page) => {
    router.visit(route("internal.vehicles.index", { page }));
};

const editModalOpen = ref(false);
const editForm = ref({
    id: "",
    brand: "",
    plate_no: "",
    country_of_origin: "",
    manufacturer: "",
    model: "",
    color: "",
    type: "",
    year: new Date().getFullYear(),
    mileage: "",
    condition: "New",
    rental_rate: "",
    status: "Available",
    purchase_cost: 0,
    purchase_date: "",
    image: null,
});

const imagePreview = ref(null);
const editErrors = ref({});

const vehicleTypes = [
    "Sport Bike",
    "Cruiser",
    "Touring",
    "Standard",
    "Dual Sport",
    "Scooter",
];

const conditions = ["New", "Used", "Excellent", "Good", "Fair"];
const statuses = ["Available", "Maintenance", "Reserved", "Rented"];

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        editForm.value.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const openEditModal = (vehicle) => {
    editForm.value = { ...vehicle };
    imagePreview.value = vehicle?.asset?.file_path;
    editModalOpen.value = true;
};

const submitEdit = () => {
    router.post(
        route("internal.vehicles.update", editForm.value.id),
        {
            ...editForm.value,
            _method: "PUT",
        },
        {
            forceFormData: true,
            onSuccess: () => {
                editModalOpen.value = false;
                editForm.value = {
                    id: "",
                    brand: "",
                    plate_no: "",
                    country_of_origin: "",
                    manufacturer: "",
                    model: "",
                    color: "",
                    type: "",
                    year: new Date().getFullYear(),
                    mileage: "",
                    condition: "New",
                    rental_rate: "",
                    purchase_cost: 0,
                    purchase_date: "",
                    status: "Available",
                    image: null,
                };
                imagePreview.value = null;
            },
            onError: (e) => {
                editErrors.value = e;
            },
        }
    );
};
</script>

<template>
    <InternalLayout>
        <Head title="Vehicle Management" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">
                            Vehicles
                        </h1>
                        <p class="mt-1 text-sm text-gray-600">
                            Manage your vehicle inventory
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- View Toggle -->
                        <div
                            class="flex items-center bg-white rounded-lg shadow p-1"
                        >
                            <button
                                @click="viewMode = 'table'"
                                :class="[
                                    'p-2 rounded-md',
                                    viewMode === 'table'
                                        ? 'bg-red-50 text-red-600'
                                        : 'text-gray-500 hover:text-red-600',
                                ]"
                            >
                                <TableCellsIcon class="w-5 h-5" />
                            </button>
                            <button
                                @click="viewMode = 'card'"
                                :class="[
                                    'p-2 rounded-md',
                                    viewMode === 'card'
                                        ? 'bg-red-50 text-red-600'
                                        : 'text-gray-500 hover:text-red-600',
                                ]"
                            >
                                <ViewColumnsIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <Link
                            :href="route('internal.vehicles.create')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                            method="get"
                        >
                            <PlusIcon class="w-5 h-5 mr-2" />
                            Add Vehicle
                        </Link>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search vehicles..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                    />
                </div>

                <!-- Table View -->
                <DataTable
                    v-if="viewMode === 'table'"
                    :headers="headers"
                    :items="filteredVehicles"
                    class="mb-6"
                >
                    <template #default="{ item }">
                        <tr class="hover:bg-gray-50">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                            >
                                {{ item.id }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                {{ item.brand }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                {{ item.model }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                {{ item.plate_number }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                            >
                                ₱{{ item.rental_rate.toLocaleString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="getStatusColor(item.status)"
                                >
                                    {{ item.status }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <button
                                    @click="openEditModal(item)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    </template>
                </DataTable>

                <!-- Card View -->
                <div
                    v-else
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="vehicle in filteredVehicles"
                        :key="vehicle.id"
                        class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow"
                    >
                        <div class="aspect-w-16 aspect-h-9">
                            <img
                                :src="
                                    vehicle?.image?.file_path ||
                                    '/images/placeholder-vehicle.jpg'
                                "
                                :alt="vehicle.brand + ' ' + vehicle.model"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3
                                        class="text-lg font-semibold text-gray-900"
                                    >
                                        {{ vehicle.brand }} {{ vehicle.model }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ vehicle.plate_number }}
                                    </p>
                                </div>
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="getStatusColor(vehicle.status)"
                                >
                                    {{ vehicle.status }}
                                </span>
                            </div>
                            <div class="mt-4 flex justify-between items-center">
                                <p class="text-lg font-bold text-red-600">
                                    ₱{{
                                        vehicle.rental_rate.toLocaleString()
                                    }}/day
                                </p>
                                <Link
                                    :href="
                                        route(
                                            'internal.vehicles.edit',
                                            vehicle.id
                                        )
                                    "
                                    class="text-sm text-red-600 hover:text-red-700"
                                >
                                    Edit
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <Pagination
                    @page-change="changePage"
                    :currentPage="vehicles.current_page"
                    :totalPages="vehicles.to"
                    :itemsPerPage="vehicles.per_page"
                    :totalItems="vehicles.total"
                />
            </div>
        </div>
    </InternalLayout>

    <Modal v-model="editModalOpen" size="xl">
        <template #header>
            <h2 class="text-xl font-bold text-gray-800">Edit Vehicle</h2>
        </template>

        <template #default>
            <form @submit.prevent="submitEdit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Image Upload -->
                    <div class="col-span-full">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Vehicle Image
                        </label>
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <div
                                    v-if="imagePreview"
                                    class="relative w-40 h-40"
                                >
                                    <img
                                        :src="imagePreview"
                                        class="object-cover rounded-lg w-full h-full"
                                    />
                                    <button
                                        type="button"
                                        @click="
                                            imagePreview = null;
                                            editForm.image = null;
                                        "
                                        class="absolute top-0 right-0 p-1 bg-red-600 text-white rounded-full -mt-2 -mr-2"
                                    >
                                        <span class="sr-only">Remove</span>
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                <div
                                    v-else
                                    class="w-40 h-40 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center"
                                >
                                    <label class="cursor-pointer">
                                        <span
                                            class="flex items-center justify-center"
                                        >
                                            <svg
                                                class="h-12 w-12 text-gray-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 4v16m8-8H4"
                                                />
                                            </svg>
                                        </span>
                                        <input
                                            type="file"
                                            class="sr-only"
                                            @change="handleImageUpload"
                                            accept="image/*"
                                        />
                                    </label>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p>
                                    Upload a high-quality image of the vehicle
                                </p>
                                <p class="mt-1">JPG, PNG or GIF up to 10MB</p>
                            </div>
                        </div>
                        <div
                            v-if="editErrors.image"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ editErrors.image }}
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Brand</label
                            >
                            <input
                                type="text"
                                v-model="editForm.brand"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            />
                            <div
                                v-if="editErrors.brand"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ editErrors.brand }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Model</label
                            >
                            <input
                                type="text"
                                v-model="editForm.model"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            />
                            <div
                                v-if="editErrors.model"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ editErrors.model }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Purchase Cost</label
                            >
                            <input
                                type="number"
                                v-model="editForm.purchase_cost"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            />
                            <div
                                v-if="editErrors.purchase_cost"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ editErrors.purchase_cost }}
                            </div>
                        </div>

                         <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Purchase Date</label
                            >
                            <input
                                type="date"
                                v-model="editForm.purchase_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            />
                            <div
                                v-if="editErrors.purchase_date"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ editErrors.purchase_date }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Status</label
                            >
                            <select
                                v-model="editForm.status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                                <option
                                    v-for="status in statuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ status }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Type</label
                            >
                            <select
                                v-model="editForm.type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            >
                                <option
                                    v-for="type in vehicleTypes"
                                    :key="type"
                                    :value="type"
                                >
                                    {{ type }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Daily Rental Rate</label
                            >
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                >
                                    <span class="text-gray-500 sm:text-sm"
                                        >₱</span
                                    >
                                </div>
                                <input
                                    type="number"
                                    v-model="editForm.rental_rate"
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <div class="flex justify-end space-x-3">
                <button
                    type="button"
                    @click="editModalOpen = false"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    @click="submitEdit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Save Changes
                </button>
            </div>
        </template>
    </Modal>
</template>
