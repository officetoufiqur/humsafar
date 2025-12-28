<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, watchEffect } from 'vue';
import { ChevronRightIcon } from 'lucide-vue-next';
import { type NavItem } from '@/types';
import {
  SidebarMenu,
  SidebarMenuItem,
  SidebarMenuButton
} from '@/components/ui/sidebar';

const props = defineProps<{
  items: NavItem[];
}>();

const page = usePage();
const openMenus = ref<Record<string, boolean>>({});

function toggleMenu(title: string) {
  openMenus.value[title] = !openMenus.value[title];
}

function hasActiveSubMenu(item: NavItem): boolean {
  return item.subMenu?.some(sub =>
    sub.href === page.url || hasActiveSubMenu(sub)
  ) ?? false;
}

watchEffect(() => {
  props.items.forEach((item) => {
    if (item.subMenu && hasActiveSubMenu(item)) {
      openMenus.value[item.title] = true;
    }
  });
});
</script>

<template>
  <template v-for="item in items" :key="item.title">
    <!-- Recursive Submenu Item -->
    <SidebarMenuItem v-if="item.subMenu && item.subMenu.length">
      <SidebarMenuButton
        :tooltip="item.title"
        @click="toggleMenu(item.title)"
        class="flex justify-between cursor-pointer items-center w-full"
      >
        <div class="flex items-center gap-2">
          <component :is="item.icon" class="w-5 h-5" />
          <span class="text-[16px]">{{ item.title }}</span>
        </div>
        <ChevronRightIcon
          class="w-4 h-4 transition-transform duration-200"
          :class="{ 'rotate-90': openMenus[item.title] }"
        />
      </SidebarMenuButton>

      <transition name="fade" mode="out-in">
        <SidebarMenu
          v-show="openMenus[item.title]"
          class="pl-11 transition-all duration-200 mt-3 text-[16px]"
        >
          <!-- Recursive Call -->
          <SidebarRecursiveMenu :items="item.subMenu" />
        </SidebarMenu>
      </transition>
    </SidebarMenuItem>

    <!-- Leaf Menu Item -->
    <SidebarMenuItem v-else>
      <SidebarMenuButton
        as-child
        :is-active="item.href === page.url"
        :tooltip="item.title"
      >
        <Link :href="item.href ?? '#'" class="flex items-center gap-2">
          <component :is="item.icon" class="w-4 h-4"/>
          <span class="text-[15px]">{{ item.title }}</span>
        </Link>
      </SidebarMenuButton>
    </SidebarMenuItem>
  </template>
</template>