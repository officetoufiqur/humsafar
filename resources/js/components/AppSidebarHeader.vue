<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import NavUser from '@/components/NavUser.vue';
import { Bell } from 'lucide-vue-next';
import { ref } from 'vue';


withDefaults(defineProps<{
    breadcrumbs?: BreadcrumbItemType[];
}>(), {
    breadcrumbs: () => []
});

const notification = ref(false);
const toggleNotification = () => {
    notification.value = !notification.value
}

// const page = usePage();

// const notifications = page.props.auth.notifications;


</script>

<template>
    <header
        class="flex justify-between h-15 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-15 md:px-4">
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="flex items-center">
            <!-- <div>2000</div> -->
            <div class="mr-3 relative">
                <a class="cursor-pointer lg:text-2xl text-xl" @click="toggleNotification">
                    <Bell class="text-[#56274E] lg:text-2xl text-xl" />
                    <!-- <span v-if="notifications.length > 0"
                        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{
                        notifications.length }}</span> -->
                </a>

                <!-- Dropdown menu -->
                <div class="z-50 my-4 w-100 absolute border text-base list-none shadow bg-white divide-y divide-gray-100 rounded-lg right-0"
                    :class="{ 'hidden': !notification }" id="notification_dropdown">
                    <ul class="py-2" aria-labelledby="user-menu-button">
                        <!-- <template v-if="notifications.length > 0">
                            <li v-for="notif in notifications" :key="notif.id">
                                <Link href="/mark-as-read"
                                    class="block px-4 py-2 text-sm text-[#0F79BC] font-semibold hover:bg-gray-100">
                                {{ notif.data.message }}
                                </Link>
                            </li>
                        </template> -->
                        <template>
                            <li>
                                <p class="px-4 py-2 text-sm text-gray-500">No notifications found</p>
                            </li>
                        </template>
                    </ul>


                </div>
            </div>

            <NavUser />
        </div>
    </header>
</template>
