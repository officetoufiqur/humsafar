<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import { Sidebar, SidebarContent, SidebarHeader, SidebarMenu, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
    icon: LayoutGrid,
  },
];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <div class="mt-1 group/sidebar-wrapper" data-collapsible="icon">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <Link href="dashboard">
                        <div class="flex gap-2">
                            <!-- <div class="flex aspect-square size-9 items-center justify-center rounded-md mt-3">
                                <img class="w-14" src="/assets/images/fabicon.png" alt="icon">
                            </div> -->
                            <div class="grid flex-1 text-left text-sm my-4">
                                <img class="w-28" src="/assets/images/logo.png" alt="">
                            </div>
                        </div>
                        </Link>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>
        </div>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <!-- <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter> -->
    </Sidebar>
    <slot />
</template>
