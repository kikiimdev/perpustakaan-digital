<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, LogIn, UserPlus, Eye, Heart } from 'lucide-vue-next';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { login } from '@/routes';
import type { Buku } from '@/types';

defineProps<{
    recentBooks?: Buku[];
    mostReadBooks?: Buku[];
    mostFavoriteBooks?: Buku[];
}>();
</script>

<template>
    <Head title="Perpustakaan Digital" />

    <div
        class="flex min-h-screen flex-col bg-gradient-to-b from-background to-muted/30"
    >
        <!-- Navbar -->
        <header
            class="sticky top-0 z-50 border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/70"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6"
            >
                <Link
                    href="/"
                    class="flex items-center gap-2 text-lg font-semibold"
                >
                    <!-- <Library class="h-6 w-6 text-primary" /> -->
                    <div class="h-6 w-6 text-primary">
                        <AppLogoIcon />
                    </div>
                    <span>Perpustakaan Digital</span>
                </Link>
                <nav class="flex items-center gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link href="/app/dasbor">
                            <Button>Dashboard</Button>
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="login()">
                            <Button variant="ghost" size="sm">
                                <LogIn class="mr-1.5 h-4 w-4" />
                                Masuk
                            </Button>
                        </Link>
                        <Link href="/register">
                            <Button size="sm">
                                <UserPlus class="mr-1.5 h-4 w-4" />
                                Daftar
                            </Button>
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero -->
        <section
            class="mx-auto flex max-w-7xl flex-col items-center px-6 py-24 text-center"
        >
            <div
                class="mb-6 rounded-full border bg-muted/50 px-4 py-1.5 text-sm text-muted-foreground"
            >
                📚 Ribuan buku siap dibaca kapan saja
            </div>
            <h1 class="max-w-3xl text-4xl font-bold tracking-tight sm:text-6xl">
                Temukan Dunia Baru Melalui Setiap Halaman
            </h1>
            <p class="mt-6 max-w-2xl text-lg text-muted-foreground">
                Jelajahi koleksi buku digital kami, tandai halaman favorit, dan
                lacak kemajuan membaca Anda — semuanya dalam satu tempat.
            </p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <template v-if="$page.props.auth.user">
                    <Link href="/app/jelajahi">
                        <Button size="lg" class="text-base">
                            <BookOpen class="mr-2 h-5 w-5" />
                            Jelajahi Buku
                        </Button>
                    </Link>
                </template>
                <template v-else>
                    <Link href="/register">
                        <Button size="lg" class="text-base">
                            <UserPlus class="mr-2 h-5 w-5" />
                            Daftar Gratis
                        </Button>
                    </Link>
                    <Link :href="login()">
                        <Button variant="outline" size="lg" class="text-base">
                            <LogIn class="mr-2 h-5 w-5" />
                            Masuk
                        </Button>
                    </Link>
                </template>
            </div>
        </section>

        <!-- Features -->
        <section class="border-t bg-muted/20 py-24">
            <div class="mx-auto max-w-7xl px-6">
                <h2 class="text-center text-3xl font-bold tracking-tight">
                    Kenapa Perpustakaan Digital Kami?
                </h2>
                <p class="mt-4 text-center text-muted-foreground">
                    Dibangun untuk memberikan pengalaman membaca terbaik.
                </p>

                <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl border bg-card p-6 text-center">
                        <div
                            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <BookOpen class="h-6 w-6 text-primary" />
                        </div>
                        <h3 class="font-semibold">Koleksi Lengkap</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Akses puluhan buku dari berbagai kategori — fiksi,
                            non-fiksi, sains, sejarah, dan banyak lagi.
                        </p>
                    </div>

                    <div class="rounded-xl border bg-card p-6 text-center">
                        <div
                            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <svg
                                class="h-6 w-6 text-primary"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
                                />
                            </svg>
                        </div>
                        <h3 class="font-semibold">Tandai Halaman</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Simpan markah di halaman tertentu dan lanjutkan
                            membaca tepat di mana Anda berhenti.
                        </p>
                    </div>

                    <div class="rounded-xl border bg-card p-6 text-center">
                        <div
                            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10"
                        >
                            <svg
                                class="h-6 w-6 text-primary"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                />
                            </svg>
                        </div>
                        <h3 class="font-semibold">Statistik Membaca</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Lacak berapa buku dan halaman yang sudah Anda baca.
                            Lihat progres dan tetap termotivasi.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Book Showcase -->
        <section class="mx-auto max-w-7xl px-6 py-24">
            <div class="mb-12 text-center">
                <h2 class="text-3xl font-bold tracking-tight">
                    Jelajahi Koleksi Kami
                </h2>
                <p class="mt-4 text-muted-foreground">
                    Temukan buku-buku terbaik yang sedang tren bulan ini.
                </p>
            </div>

            <div class="space-y-16">
                <!-- Most Recent -->
                <div v-if="recentBooks && recentBooks.length > 0">
                    <h3
                        class="mb-6 flex items-center gap-2 text-xl font-semibold"
                    >
                        <BookOpen class="h-5 w-5 text-primary" />
                        Baru Ditambahkan
                    </h3>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <Link
                            v-for="book in recentBooks"
                            :key="book.id"
                            :href="`/buku/${book.id}`"
                            class="group flex flex-col overflow-hidden rounded-xl border bg-card transition-all hover:shadow-md"
                        >
                            <div class="aspect-[3/4] w-full bg-muted">
                                <img
                                    v-if="book.sampul"
                                    :src="book.sampul"
                                    :alt="book.judul"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center"
                                >
                                    <BookOpen
                                        class="h-12 w-12 text-muted-foreground/50"
                                    />
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <h4
                                    class="line-clamp-2 font-semibold group-hover:text-primary"
                                >
                                    {{ book.judul }}
                                </h4>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ book.penulis?.nama }}
                                </p>
                                <div class="mt-auto flex flex-wrap gap-1 pt-4">
                                    <span
                                        v-for="k in book.kategori?.slice(0, 2)"
                                        :key="k.id"
                                        class="rounded bg-muted px-2 py-0.5 text-xs"
                                    >
                                        {{ k.nama }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Most Read -->
                <div v-if="mostReadBooks && mostReadBooks.length > 0">
                    <h3
                        class="mb-6 flex items-center gap-2 text-xl font-semibold"
                    >
                        <Eye class="h-5 w-5 text-primary" />
                        Paling Banyak Dibaca Bulan Ini
                    </h3>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <Link
                            v-for="book in mostReadBooks"
                            :key="book.id"
                            :href="`/buku/${book.id}`"
                            class="group flex flex-col overflow-hidden rounded-xl border bg-card transition-all hover:shadow-md"
                        >
                            <div class="aspect-[3/4] w-full bg-muted">
                                <img
                                    v-if="book.sampul"
                                    :src="book.sampul"
                                    :alt="book.judul"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center"
                                >
                                    <BookOpen
                                        class="h-12 w-12 text-muted-foreground/50"
                                    />
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <h4
                                    class="line-clamp-2 font-semibold group-hover:text-primary"
                                >
                                    {{ book.judul }}
                                </h4>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ book.penulis?.nama }}
                                </p>
                                <div class="mt-auto flex flex-wrap gap-1 pt-4">
                                    <span
                                        v-for="k in book.kategori?.slice(0, 2)"
                                        :key="k.id"
                                        class="rounded bg-muted px-2 py-0.5 text-xs"
                                    >
                                        {{ k.nama }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Most Favorite -->
                <div v-if="mostFavoriteBooks && mostFavoriteBooks.length > 0">
                    <h3
                        class="mb-6 flex items-center gap-2 text-xl font-semibold"
                    >
                        <Heart class="h-5 w-5 text-primary" />
                        Paling Difavoritkan Bulan Ini
                    </h3>
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <Link
                            v-for="book in mostFavoriteBooks"
                            :key="book.id"
                            :href="`/buku/${book.id}`"
                            class="group flex flex-col overflow-hidden rounded-xl border bg-card transition-all hover:shadow-md"
                        >
                            <div class="aspect-[3/4] w-full bg-muted">
                                <img
                                    v-if="book.sampul"
                                    :src="book.sampul"
                                    :alt="book.judul"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center"
                                >
                                    <BookOpen
                                        class="h-12 w-12 text-muted-foreground/50"
                                    />
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <h4
                                    class="line-clamp-2 font-semibold group-hover:text-primary"
                                >
                                    {{ book.judul }}
                                </h4>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ book.penulis?.nama }}
                                </p>
                                <div class="mt-auto flex flex-wrap gap-1 pt-4">
                                    <span
                                        v-for="k in book.kategori?.slice(0, 2)"
                                        :key="k.id"
                                        class="rounded bg-muted px-2 py-0.5 text-xs"
                                    >
                                        {{ k.nama }}
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t py-8 text-center text-sm text-muted-foreground">
            <p>
                &copy; {{ new Date().getFullYear() }} Perpustakaan Digital.
                Dibangun dengan Laravel &amp; Vue.
            </p>
        </footer>
    </div>
</template>
