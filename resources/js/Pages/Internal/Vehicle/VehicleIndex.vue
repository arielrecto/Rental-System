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
                                {{ item.rental_rate != null ? '₱' + Number(item.rental_rate).toLocaleString() : '—' }}
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
            <!-- Validation error summary -->
            <div v-if="Object.keys(editErrors).length" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm font-semibold text-red-700 mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    <li v-for="(msg, field) in editErrors" :key="field" class="text-sm text-red-600">{{ msg }}</li>
                </ul>
            </div>

            <form @submit.prevent="submitEdit" class="space-y-6">
                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Image</label>
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div v-if="imagePreview" class="relative w-32 h-32">
                                <img :src="imagePreview" class="object-cover rounded-lg w-full h-full" />
                                <button type="button" @click="imagePreview = null; editForm.image = null;"
                                    class="absolute top-0 right-0 p-1 bg-red-600 text-white rounded-full -mt-2 -mr-2">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div v-else class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer hover:border-red-400 transition">
                                <label class="cursor-pointer flex flex-col items-center gap-1">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="text-xs text-gray-400">Upload</span>
                                    <input type="file" class="sr-only" @change="handleImageUpload" accept="image/*" />
                                </label>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">JPG, PNG or GIF — max 10MB</p>
                    </div>
                    <p v-if="editErrors.image" class="mt-1 text-sm text-red-600">{{ editErrors.image }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Brand -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Brand <span class="text-red-500">*</span></label>
                        <input type="text" v-model="editForm.brand"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.brand ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.brand" class="mt-1 text-sm text-red-600">{{ editErrors.brand }}</p>
                    </div>

                    <!-- Model -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Model <span class="text-red-500">*</span></label>
                        <input type="text" v-model="editForm.model"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.model ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.model" class="mt-1 text-sm text-red-600">{{ editErrors.model }}</p>
                    </div>

                    <!-- Manufacturer -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Manufacturer</label>
                        <input type="text" v-model="editForm.manufacturer"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.manufacturer ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.manufacturer" class="mt-1 text-sm text-red-600">{{ editErrors.manufacturer }}</p>
                    </div>

                    <!-- Plate No -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Plate Number <span class="text-red-500">*</span></label>
                        <input type="text" v-model="editForm.plate_no"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.plate_no ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.plate_no" class="mt-1 text-sm text-red-600">{{ editErrors.plate_no }}</p>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
                        <select v-model="editForm.type"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.type ? 'border-red-400 bg-red-50' : 'border-gray-300']">
                            <option value="">— Select type —</option>
                            <option v-for="t in vehicleTypes" :key="t" :value="t">{{ t }}</option>
                        </select>
                        <p v-if="editErrors.type" class="mt-1 text-sm text-red-600">{{ editErrors.type }}</p>
                    </div>

                    <!-- Color -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Color</label>
                        <input type="text" v-model="editForm.color"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.color ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.color" class="mt-1 text-sm text-red-600">{{ editErrors.color }}</p>
                    </div>

                    <!-- Year -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Year</label>
                        <input type="number" v-model="editForm.year" min="1990" :max="new Date().getFullYear() + 1"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.year ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.year" class="mt-1 text-sm text-red-600">{{ editErrors.year }}</p>
                    </div>

                    <!-- Mileage -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mileage (km) <span class="text-red-500">*</span></label>
                        <input type="text" v-model="editForm.mileage" placeholder="e.g. 5000"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.mileage ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.mileage" class="mt-1 text-sm text-red-600">{{ editErrors.mileage }}</p>
                    </div>

                    <!-- Condition -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Condition</label>
                        <select v-model="editForm.condition"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.condition ? 'border-red-400 bg-red-50' : 'border-gray-300']">
                            <option value="">— Select condition —</option>
                            <option v-for="c in conditions" :key="c" :value="c">{{ c }}</option>
                        </select>
                        <p v-if="editErrors.condition" class="mt-1 text-sm text-red-600">{{ editErrors.condition }}</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select v-model="editForm.status"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.status ? 'border-red-400 bg-red-50' : 'border-gray-300']">
                            <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                        </select>
                        <p v-if="editErrors.status" class="mt-1 text-sm text-red-600">{{ editErrors.status }}</p>
                    </div>

                    <!-- Daily Rental Rate -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Daily Rental Rate</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" v-model="editForm.rental_rate"
                                :class="['pl-7 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.rental_rate ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        </div>
                        <p v-if="editErrors.rental_rate" class="mt-1 text-sm text-red-600">{{ editErrors.rental_rate }}</p>
                    </div>

                    <!-- Country of Origin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Country of Origin</label>
                        <input type="text" v-model="editForm.country_of_origin"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.country_of_origin ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.country_of_origin" class="mt-1 text-sm text-red-600">{{ editErrors.country_of_origin }}</p>
                    </div>

                    <!-- Purchase Cost -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Cost</label>
                        <input type="number" v-model="editForm.purchase_cost"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.purchase_cost ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.purchase_cost" class="mt-1 text-sm text-red-600">{{ editErrors.purchase_cost }}</p>
                    </div>

                    <!-- Purchase Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
                        <input type="date" v-model="editForm.purchase_date"
                            :class="['mt-1 block w-full rounded-md shadow-sm focus:ring-red-500 focus:border-red-500', editErrors.purchase_date ? 'border-red-400 bg-red-50' : 'border-gray-300']" />
                        <p v-if="editErrors.purchase_date" class="mt-1 text-sm text-red-600">{{ editErrors.purchase_date }}</p>
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
