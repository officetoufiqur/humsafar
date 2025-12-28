<script setup lang="ts">
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

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
    <div class="h-[38vh] w-full relative">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold mb-4">{{ title }}</h2>
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

        <div class="mt-4 text-sm text-gray-600 absolute bottom-0 w-full">
            <div class="flex items-center justify-between">
                <div>
                    Showing
                    {{ (currentPage - 1) * entriesPerPage + 1 }}
                    to
                    {{
                        Math.min(currentPage * entriesPerPage, filteredData.length)
                    }}
                    of {{ filteredData.length }} entries
                </div>
                <div class="flex gap-1 float-end">
                    <button @click="currentPage--" :disabled="currentPage === 1"
                        class="px-2 py-1 border rounded disabled:opacity-50">
                        <chevron-left-icon class="w-4 h-4" />
                    </button>
                    <button class="px-2 py-1 border rounded bg-[#56284F] text-white">{{ currentPage }}</button>
                    <button @click="currentPage++" :disabled="currentPage >= totalPages"
                        class="px-2 py-1 border rounded disabled:opacity-50">
                        <chevron-right-icon class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>