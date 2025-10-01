<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();
const isAuthenticated = ref(page.props.auth.user !== null);

// Hardcoded motorcycle data
const motorcycles = [
    {
        id: 1,
        name: 'Kawasaki Ninja 400',
        price: 2500,
        image: 'https://example.com/images/motorcycles/ninja-400.jpg',
        category: 'Sport',
        cc: '400cc',
        status: 'Available',
        description: 'Perfect for beginners and experienced riders alike.'
    },
    {
        id: 2,
        name: 'Honda CBR600RR',
        price: 3000,
        image: 'https://example.com/images/motorcycles/cbr600rr.jpg',
        category: 'Sport',
        cc: '600cc',
        status: 'Available',
        description: 'A high-performance supersport motorcycle for the track.'
    },
    {
        id: 3,
        name: 'Yamaha YZF-R3',
        price: 2400,
        image: 'https://example.com/images/motorcycles/yzf-r3.jpg',
        category: 'Sport',
        cc: '321cc',
        status: 'Rented',
        description: 'Lightweight and agile, ideal for city and canyon carving.'
    },
    {
        id: 4,
        name: 'Suzuki GSX-R750',
        price: 3200,
        image: 'https://example.com/images/motorcycles/gsx-r750.jpg',
        category: 'Sport',
        cc: '750cc',
        status: 'Available',
        description: 'The original race replica, perfectly balanced performance.'
    },
    {
        id: 5,
        name: 'Ducati Panigale V2',
        price: 4500,
        image: 'https://example.com/images/motorcycles/panigale-v2.jpg',
        category: 'Sport',
        cc: '955cc',
        status: 'Available',
        description: 'Italian design meets thrilling twin-cylinder power.'
    },
    {
        id: 6,
        name: 'Harley-Davidson Iron 883',
        price: 2800,
        image: 'https://example.com/images/motorcycles/iron-883.jpg',
        category: 'Cruiser',
        cc: '883cc',
        status: 'Available',
        description: 'Iconic American cruiser with a raw, blacked-out style.'
    },
    {
        id: 7,
        name: 'Yamaha MT-07',
        price: 2700,
        image: 'https://example.com/images/motorcycles/mt-07.jpg',
        category: 'Naked',
        cc: '689cc',
        status: 'Maintenance',
        description: 'Torquey and fun, the master of hyper naked bikes.'
    },
    {
        id: 8,
        name: 'KTM 390 Duke',
        price: 2300,
        image: 'https://example.com/images/motorcycles/ktm-390-duke.jpg',
        category: 'Naked',
        cc: '373cc',
        status: 'Available',
        description: 'The corner rocket - nimble and packed with attitude.'
    },
    {
        id: 9,
        name: 'BMW R 1250 GS',
        price: 4800,
        image: 'https://example.com/images/motorcycles/r1250gs.jpg',
        category: 'Adventure',
        cc: '1254cc',
        status: 'Available',
        description: 'The ultimate adventure bike, ready for any terrain.'
    },
    {
        id: 10,
        name: 'Honda Africa Twin',
        price: 4200,
        image: 'https://example.com/images/motorcycles/africa-twin.jpg',
        category: 'Adventure',
        cc: '1084cc',
        status: 'Available',
        description: 'Legendary off-road capability and long-distance comfort.'
    },
    {
        id: 11,
        name: 'Kawasaki Vulcan S',
        price: 2600,
        image: 'https://example.com/images/motorcycles/vulcan-s.jpg',
        category: 'Cruiser',
        cc: '649cc',
        status: 'Rented',
        description: 'A sporty cruiser that defies convention with its unique styling.'
    },
    {
        id: 12,
        name: 'Triumph Bonneville T120',
        price: 3500,
        image: 'https://example.com/images/motorcycles/bonneville-t120.jpg',
        category: 'Classic',
        cc: '1200cc',
        status: 'Available',
        description: 'Timeless British style with modern performance.'
    },
    {
        id: 13,
        name: 'Honda Gold Wing',
        price: 5500,
        image: 'https://example.com/images/motorcycles/gold-wing.jpg',
        category: 'Touring',
        cc: '1833cc',
        status: 'Available',
        description: 'The pinnacle of motorcycle touring comfort and luxury.'
    },
    {
        id: 14,
        name: 'Vespa GTS 300',
        price: 1800,
        image: 'https://example.com/images/motorcycles/vespa-gts-300.jpg',
        category: 'Scooter',
        cc: '300cc',
        status: 'Available',
        description: 'Classic Italian style meets modern scooter convenience.'
    },
    {
        id: 15,
        name: 'Yamaha XMAX',
        price: 1900,
        image: 'https://example.com/images/motorcycles/xmax.jpg',
        category: 'Scooter',
        cc: '292cc',
        status: 'Available',
        description: 'A sporty and practical maxi-scooter for the city and beyond.'
    },
    {
        id: 16,
        name: 'Triumph Street Triple R',
        price: 3100,
        image: 'https://example.com/images/motorcycles/street-triple-r.jpg',
        category: 'Naked',
        cc: '765cc',
        status: 'Available',
        description: 'Aggressive looks and an exhilarating three-cylinder engine.'
    },
    {
        id: 17,
        name: 'Aprilia RS 660',
        price: 3800,
        image: 'https://example.com/images/motorcycles/rs-660.jpg',
        category: 'Sport',
        cc: '659cc',
        status: 'Available',
        description: 'A perfect blend of road comfort and track-day performance.'
    },
    {
        id: 18,
        name: 'Royal Enfield Classic 350',
        price: 2000,
        image: 'https://example.com/images/motorcycles/classic-350.jpg',
        category: 'Classic',
        cc: '349cc',
        status: 'Available',
        description: 'Post-war styling with a reliable and charming engine.'
    },
    {
        id: 19,
        name: 'Kawasaki Z900',
        price: 2900,
        image: 'https://example.com/images/motorcycles/z900.jpg',
        category: 'Naked',
        cc: '948cc',
        status: 'Maintenance',
        description: 'Sugomi-inspired design with a powerful inline-four engine.'
    },
    {
        id: 20,
        name: 'Indian Scout Bobber',
        price: 3300,
        image: 'https://example.com/images/motorcycles/scout-bobber.jpg',
        category: 'Cruiser',
        cc: '1133cc',
        status: 'Available',
        description: 'A modern take on the classic bobber with a powerful V-twin.'
    }
];

const categories = ['All', 'Sport', 'Cruiser', 'Adventure', 'Touring'];
const selectedCategory = ref('All');

const filteredMotorcycles = computed(() => {
    return selectedCategory.value === 'All'
        ? motorcycles
        : motorcycles.filter(moto => moto.category === selectedCategory.value);
});

const handleRent = (motorcycle) => {
    // Handle rental logic here
};
</script>

<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="mb-8 text-center">
                    <h1 class="text-4xl font-bold text-gray-800">Available Motorcycles</h1>
                    <p class="mt-2 text-gray-600">Choose your perfect ride from our premium collection</p>
                </div>

                <!-- Category Filter -->
                <div class="flex justify-center mb-8 space-x-4">
                    <button
                        v-for="category in categories"
                        :key="category"
                        @click="selectedCategory = category"
                        :class="[
                            'px-4 py-2 rounded-lg transition duration-300',
                            selectedCategory === category
                                ? 'bg-red-600 text-white'
                                : 'bg-white text-gray-700 hover:bg-red-50'
                        ]"
                    >
                        {{ category }}
                    </button>
                </div>

                <!-- Motorcycles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="motorcycle in filteredMotorcycles"
                        :key="motorcycle.id"
                        class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300"
                    >
                        <img
                            :src="motorcycle.image"
                            :alt="motorcycle.name"
                            class="w-full h-48 object-cover"
                        />
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-800">{{ motorcycle.name }}</h3>
                                    <p class="text-gray-600">{{ motorcycle.category }} | {{ motorcycle.cc }}</p>
                                </div>
                                <span class="text-red-600 font-bold">₱{{ motorcycle.price }}/day</span>
                            </div>
                            <p class="text-gray-600 mb-4">{{ motorcycle.description }}</p>
                            <div class="flex justify-between items-center">
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm',
                                        motorcycle.status === 'Available'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    ]"
                                >
                                    {{ motorcycle.status }}
                                </span>
                                <button
                                    v-if="isAuthenticated"
                                    @click="handleRent(motorcycle)"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition duration-300"
                                    :disabled="motorcycle.status !== 'Available'"
                                >
                                    Rent Now
                                </button>
                                <Link
                                    v-else
                                    :href="route('login')"
                                    class="text-red-600 hover:text-red-700 font-semibold"
                                >
                                    Login to Rent
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.grid {
    @apply gap-6;
}

.motorcycle-card {
    @apply transition-all duration-300 ease-in-out;
}

.motorcycle-card:hover {
    @apply transform scale-105;
}
</style>
