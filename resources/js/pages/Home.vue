<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>


<template>

    <Head title="Welcome" />

    <div class="relative min-h-screen text-[#1b1b18] dark:text-[#EDEDEC]">
        <div class="absolute inset-0 bg-image"></div>
        <!-- Overlay -->
        <div class="absolute inset-0 bg-[#111]/40"></div>

        <!-- Content -->
        <div class="relative z-10 flex min-h-screen flex-col items-center p-6 lg:justify-center lg:p-8">

            <!-- Header -->
            <header class="mb-6 w-full max-w-4xl text-sm">
                <nav class="flex items-center justify-end gap-4">
                    <Link v-if="$page.props.auth.user" :href="dashboard()"
                        class="rounded-sm border px-5 py-1.5 text-sm hover:border-gray-400 text-white font-medium">
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link :href="login()" class="rounded-sm px-5 py-1.5 text-sm hover:underline text-white font-medium">
                            Log in
                        </Link>

                        <Link v-if="canRegister" :href="register()"
                            class="rounded-sm border px-5 py-1.5 text-sm hover:border-gray-400 text-white font-medium">
                            Register
                        </Link>
                    </template>
                </nav>
            </header>

            <!-- Main Content -->
            <main class="flex grow items-center justify-center">
                <h1 class="text-4xl font-bold text-white lg:text-6xl mt-10">
                    Welcome To Humsafar Matrimony
                </h1>
            </main>

        </div>
    </div>
</template>

<style>
.bg-image {
    background-image: url('/assets/images/woman.png');
    background-size: cover;
    background-position: center;
}
</style>