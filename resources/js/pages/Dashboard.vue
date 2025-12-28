<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import BarChart from '@/components/admin/BarChart.vue';
import LineChart from '@/components/admin/LineChart.vue';
import Table from '@/components/admin/Table.vue';
import { Building2Icon } from 'lucide-vue-next';
import Card from '@/components/admin/Card.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const column = [
    { label: 'Name', key: 'name' },
    { label: 'Email', key: 'email' },
]

const data = [
    { id: 1, name: 'John Doe', email: 'n0d0W@example.com' },
]
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3 px-4">
                <Card title="Users" value="0" />
                <Card title="Posts" value="0" />
                <Card title="Comments" value="0" />
            </div>
            <div class="flex items-center justify-center space-x-4 px-4">
                <div class="w-full h-[44vh] rounded-xl border border-sidebar-border/70 p-5">
                    <BarChart />
                </div>
                <div class="w-full h-[44vh] rounded-xl border border-sidebar-border/70 p-5">
                    <LineChart />
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 space-x-4 px-4">
                <div class="col-span-2 w-full h-[42vh] rounded-xl border border-sidebar-border/70 p-5">
                    <Table :rows="data" :columns="column" :title="`Active Users`" />
                </div>
                <div class="w-full h-[42vh] rounded-xl border border-sidebar-border/70 p-5">
                    <div class="">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing User</h3>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <Building2Icon class="h-5 w-5 text-[#56284F]" />
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">John Doe</div>
                                    <div class="text-sm text-gray-500">House Manager</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-green-600 font-medium">40%</div>
                                    <div class="text-xs text-gray-500">Growth</div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <img src="/assets/icon/nurse.svg" alt="" class="h-6 w-6 text-pink-600">
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Zaneya</div>
                                    <div class="text-sm text-gray-500">Nurse</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-green-600 font-medium">35%</div>
                                    <div class="text-xs text-gray-500">Growth</div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                    <img src="/assets/icon/nurse.svg" alt="" class="h-6 w-6 text-pink-600">
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Ariya</div>
                                    <div class="text-sm text-gray-500">Nurse</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-green-600 font-medium">35%</div>
                                    <div class="text-xs text-gray-500">Growth</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                    <img src="/assets/icon/nurse.svg" alt="" class="h-6 w-6 text-pink-600">
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Ariya</div>
                                    <div class="text-sm text-gray-500">Nurse</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-green-600 font-medium">35%</div>
                                    <div class="text-xs text-gray-500">Growth</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                    <img src="/assets/icon/nurse.svg" alt="" class="h-6 w-6 text-pink-600">
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Ariya</div>
                                    <div class="text-sm text-gray-500">Nurse</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-green-600 font-medium">35%</div>
                                    <div class="text-xs text-gray-500">Growth</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
