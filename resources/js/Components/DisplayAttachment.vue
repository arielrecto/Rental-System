<script setup>
import { computed } from 'vue';
import {
    PhotoIcon,
    DocumentTextIcon,
    ArchiveBoxIcon,
    ArrowDownTrayIcon,
    XMarkIcon
} from '@heroicons/vue/24/solid';

// 1. Props changed to accept filePath and fileExtension
const props = defineProps({
    filePath: {
        type: String,
        required: true
    },
    fileExtension: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['remove']);

// 2. New computed property to infer the MIME type from the extension
const mimeType = computed(() => {
    const ext = props.fileExtension.toLowerCase();
    switch (ext) {
        case 'jpg':
        case 'jpeg':
            return 'image/jpeg';
        case 'png':
            return 'image/png';
        case 'gif':
            return 'image/gif';
        case 'pdf':
            return 'application/pdf';
        case 'zip':
            return 'application/zip';
        default:
            return 'application/octet-stream'; // Generic binary file type
    }
});

// 3. New computed property to extract the filename from the path
const fileName = computed(() => {
    // This splits the URL by "/" and returns the last segment
    return props.filePath.split('/').pop();
});

// These computed properties now use mimeType instead of props.attachment.type
const isImage = computed(() => mimeType.value.startsWith('image/'));
const fileIcon = computed(() => {
    if (isImage.value) return PhotoIcon;
    if (mimeType.value === 'application/pdf') return DocumentTextIcon;
    if (mimeType.value === 'application/zip') return ArchiveBoxIcon;
    return DocumentTextIcon;
});

const handleRemove = () => {
    // Emit the path as a unique identifier
    emit('remove', props.filePath);
};
</script>

<template>
    <div class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
        <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-gray-100 rounded-md mr-4">
            <img
                v-if="isImage"
                :src="filePath"
                :alt="fileName"
                class="w-full h-full object-cover rounded-md"
            />
            <component
                v-else
                :is="fileIcon"
                class="w-7 h-7 text-gray-500"
            />
        </div>

        <div class="flex-grow min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
                {{ fileName }}
            </p>
        </div>

        <div class="flex-shrink-0 flex items-center ml-4 space-x-2">
            <a
                :href="filePath"
                download
                class="p-2 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                aria-label="Download"
            >
                <ArrowDownTrayIcon class="w-5 h-5" />
            </a>
            <button
                @click="handleRemove"
                class="p-2 text-gray-500 hover:text-red-600 hover:bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500"
                aria-label="Remove"
            >
                <XMarkIcon class="w-5 h-5" />
            </button>
        </div>
    </div>
</template>
