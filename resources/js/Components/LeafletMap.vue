<script setup>
import { onMounted, ref, watch , onUnmounted} from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    locations: {
        type: Array,
        default: () => [],
        // Expected format:
        // [{
        //   id: 1,
        //   lat: 14.5995,
        //   lng: 120.9842,
        //   title: "Vehicle Name",
        //   status: "Available"
        // }]
    }
});

const mapContainer = ref(null);
let map = null;
let markers = [];

// Custom motorcycle icon
const motorcycleIcon = L.icon({
    iconUrl: '/motorbike.png', // Make sure to add this image to your public folder
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32]
});

// Initialize map
const initMap = () => {
    // Metro Manila coordinates
    const metroManila = [14.5995, 120.9842];

    map = L.map(mapContainer.value).setView(metroManila, 11);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
};

// Add markers to map
const addMarkers = () => {
    // Clear existing markers
    markers.forEach(marker => marker.remove());
    markers = [];

    // Add new markers
    props.locations.forEach(location => {
        const marker = L.marker([location.lat, location.lng], { icon: motorcycleIcon })
            .bindPopup(`
                <div class="p-2">
                    <h3 class="font-bold">${location.title}</h3>
                    <p class="text-sm">Status: ${location.status}</p>
                </div>
            `)
            .addTo(map);

        markers.push(marker);
    });

    // Adjust map view to fit all markers if there are any
    if (markers.length > 0) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
};

// Watch for changes in locations prop
watch(() => props.locations, () => {
    if (map) {
        addMarkers();
    }
}, { deep: true });

onMounted(() => {
    initMap();
    addMarkers();
});

// Clean up on unmount
onUnmounted(() => {
    if (map) {
        map.remove();
    }
});
</script>

<template>
    <div ref="mapContainer" class="w-full h-[500px] rounded-lg shadow-lg"></div>
</template>

<style>
@import 'leaflet/dist/leaflet.css';

.leaflet-popup-content p {
    margin: 5px 0;
}

.leaflet-popup-content h3 {
    color: #dc2626; /* red-600 */
    margin: 0;
}
</style>
