<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import Navbar from '@/Components/Navbar.vue';
import {
    HomeIcon,
    UsersIcon,
    TruckIcon,
    ClipboardDocumentListIcon,
    ChartBarIcon,
    WrenchScrewdriverIcon,
    ShoppingBagIcon,
    DocumentChartBarIcon,
} from '@heroicons/vue/24/outline';
import Flash from '@/Components/Flash.vue';
import { usePage } from '@inertiajs/vue3';

const navigation = [
    {
        name: 'Dashboard',
        icon: HomeIcon,
        href: route('internal.dashboard'),
        isActive: route().current('internal.dashboard'),
        children: []
    },
    {
        name: 'Vehicle Management',
        icon: TruckIcon,
        href: '#',
        isActive: route().current('internal.vehicles.*'),
        children: [
            {
                name: 'All Vehicles',
                href: route('internal.vehicles.index'),
                isActive: route().current('internal.vehicles.index')
            },
            {
                name: 'Add Vehicle',
                href: route('internal.vehicles.create'),
                isActive: route().current('internal.vehicles.create')
            },
            {
                name: 'Maintenance',
                href: "#",
                isActive: route().current('internal.vehicles.maintenance')
            }
        ]
    },
    {
        name: 'Rental Orders',
        icon: ShoppingBagIcon,
        href: '#',
        isActive: route().current('internal.rental-orders.*'),
        children: [
            {
                name: 'All Orders',
                href: route('internal.rental-orders.index'),
                isActive: route().current('internal.rental-orders.index')
            },
            {
                name: 'Active Rentals',
                href: "#",
                isActive: route().current('internal.rentals.active')
            },
            {
                name: 'Pending Requests',
                href: route('internal.rental-orders.index', { status: 'pending' }),
                isActive: route().current('internal.rentals.pending')
            }
        ]
    },
    {
        name: 'User Management',
        icon: UsersIcon,
        href: '#',
        isActive: route().current('internal.user-management.*'),
        children: [
            {
                name: 'All Users',
                href: route('internal.user-management.index'),
                isActive: route().current('internal.user-management.index')
            },
            {
                name: 'Add User',
                href: route('internal.user-management.create'),
                isActive: route().current('internal.user-management.create')
            },
            {
                name: 'Roles & Permissions',
                href: "#",
                isActive: route().current('internal.users.roles')
            }
        ]
    },
    {
        name: 'Reports',
        icon: DocumentChartBarIcon,
        href: '#',
        isActive: route().current('internal.report.*'),
        children: [
            {
                name: 'Revenue Reports',
                href: route('internal.report.revenue'),
                isActive: route().current('internal.report.revenue')
            },
            {
                name: 'Rental Analytics',
                href: route('internal.report.rental-analytics'),
                isActive: route().current('internal.report.rental-analytics')
            },
            {
                name: 'Vehicle Statistics',
                href: route('internal.report.vehicle'),
                isActive: route().current('internal.report.vehicle')
            },
            {
                name: 'Customer Reports',
                href: "#",
                isActive: route().current('internal.reports.customers')
            }
        ]
    },
    {
        name: 'Maintenance',
        icon: WrenchScrewdriverIcon,
        href: '#',
        isActive: route().current('internal.maintenance.*'),
        children: [
            {
                name: 'Schedule',
                href: "#",
                isActive: route().current('internal.maintenance.schedule')
            },
            {
                name: 'History',
                href: "#",
                isActive: route().current('internal.maintenance.history')
            },
            {
                name: 'Service Records',
                href:"#",
                isActive: route().current('internal.maintenance.records')
            }
        ]
    }
];

const page = usePage();

const user = {
    name: page.props.auth.user.name,
    avatar: `https://ui-avatars.com/api/?name=${page.props.auth.user.name}&background=random&size=128`,
};


</script>

<template>

    <div class="min-h-screen flex flex-col">
        <Flash />
        <div class="flex flex-1">
            <Sidebar :navigation="navigation" />


            <div class="flex-1 flex flex-col">
                <Navbar  :user="user" :links="navigationLinks"/>

                <main class="flex-1 p-6">
                    <slot></slot>
                </main>
            </div>
        </div>
    </div>

</template>
