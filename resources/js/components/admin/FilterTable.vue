<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

interface TableData {
    [key: string]: any;
    id: number;
}

interface TableColumn {
    key: string;
    label: string;
    icon?: any;
}

const props = defineProps<{
    rows: TableData[];
    columns: TableColumn[];
    title?: string;
    createText?: string;
    createUrl?: string;
    createBtn?: boolean;
    backText?: string;
    backUrl?: string;
    backBtn?: boolean;
}>();


const entriesPerPage = ref(10);
const currentPage = ref(1);
const search = ref('');

const filteredData = computed(() =>
    props.rows.filter((item) =>
        Object.values(item)
            .join(' ')
            .toLowerCase()
            .includes(search.value.toLowerCase())
    )
);

const totalPages = computed(() =>
    Math.ceil(filteredData.value.length / entriesPerPage.value)
);

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * entriesPerPage.value;
    return filteredData.value.slice(start, start + entriesPerPage.value);
});

watch([search, entriesPerPage], () => {
    currentPage.value = 1;
});
</script>

<template>
    <div class="">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-2xl font-semibold mb-4">{{ title }}</h2>
            <Link v-if="createBtn" :href="createUrl || '#'" class="bg-[#0f79bc] hover:bg-[#4a4745] px-6 py-2 text-white rounded-md">{{ createText }}</Link>
            <Link v-if="backBtn" :href="backUrl || '#'" class="bg-[#0f79bc] hover:bg-[#4a4745] px-6 py-2 text-white rounded-md">{{ backText }}</Link>
        </div>

        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <select v-model="entriesPerPage" class="border px-2 py-1 rounded text-sm border-gray-300">
                    <option :value="5">5</option>
                    <option :value="10">10</option>
                    <option :value="20">20</option>
                </select>
                <span class="text-sm text-gray-700">entries per page</span>
            </div>
            <div class="text-sm">
                <label class="mr-2">Search:</label>
                <input v-model="search" type="text" class="border px-2 py-1 rounded border-gray-300" />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border relative ">
                <thead class="bg-gray-100 text-sm text-gray-700">
                    <tr>
                        <th v-for="col in columns" :key="col.key" class="px-4 py-4 text-left">
                            {{ col.label }}
                            <component :is="col.icon" v-if="col.icon" class="inline w-4 h-4 ml-1" />
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="paginatedData.length === 0">
                        <td :colspan="columns.length" class="text-center py-6 text-gray-500">
                            No data found
                        </td>
                    </tr>
                    <tr v-for="item in paginatedData" :key="item.id">
                        <td v-for="col in columns" :key="col.key" class="border border-gray-200 px-4 py-4">
                            <slot :name="col.key" :item="item">
                                {{ item[col.key] }}
                            </slot>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>

        <div class="flex items-center justify-between mt-4 text-sm text-gray-600">
            <div>
                Showing
                {{ (currentPage - 1) * entriesPerPage + 1 }}
                to
                {{
                    Math.min(currentPage * entriesPerPage, filteredData.length)
                }}
                of {{ filteredData.length }} entries
            </div>
            <div class="flex gap-1">
                <button @click="currentPage--" :disabled="currentPage === 1"
                    class="px-2 py-1 border rounded disabled:opacity-50">
                    ‹
                </button>
                <button class="px-2 py-1 border rounded bg-blue-600 text-white">{{ currentPage }}</button>
                <button @click="currentPage++" :disabled="currentPage >= totalPages"
                    class="px-2 py-1 border rounded disabled:opacity-50">
                    ›
                </button>
            </div>
        </div>
    </div>
</template>