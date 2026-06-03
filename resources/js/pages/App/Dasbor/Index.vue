<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Heart, Library } from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Buku, BukuFavorit, StatistikPengguna } from '@/types';

defineProps<{
    statistik: StatistikPengguna;
    rekomendasi: Buku[];
    favorit: BukuFavorit[];
}>();
</script>

<template>
    <Head title="Dasbor" />

    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">Statistik Membaca Saya</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Card>
                <CardHeader class="pb-2 flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Bacaan Bulan Ini</CardTitle>
                    <BookOpen class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold">{{ statistik.bacaan_bulan_ini }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2 flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Total Halaman</CardTitle>
                    <Library class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold">{{ statistik.total_halaman }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2 flex flex-row items-center justify-between">
                    <CardTitle class="text-sm font-medium text-muted-foreground">Buku Dibaca</CardTitle>
                    <Heart class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <p class="text-3xl font-bold">{{ statistik.buku_dibaca }}</p>
                </CardContent>
            </Card>
        </div>

        <div v-if="favorit.length" class="space-y-4">
            <h2 class="text-lg font-semibold">Buku Favorit Saya</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <Link v-for="fav in favorit" :key="fav.id" :href="`/app/buku/${fav.buku_id}`">
                    <Card class="hover:shadow-md transition-shadow cursor-pointer overflow-hidden">
                        <div class="aspect-[2/3] bg-muted flex items-center justify-center overflow-hidden">
                            <img v-if="fav.buku?.sampul" :src="fav.buku.sampul" :alt="fav.buku.judul" class="w-full h-full object-cover" />
                            <BookOpen v-else class="h-8 w-8 text-muted-foreground/50" />
                        </div>
                        <CardContent class="p-3 text-center">
                            <p class="font-medium text-sm truncate">{{ fav.buku?.judul }}</p>
                            <p class="text-xs text-muted-foreground">{{ fav.buku?.penulis?.nama }}</p>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>

        <div v-if="rekomendasi.length" class="space-y-4">
            <h2 class="text-lg font-semibold">Rekomendasi untuk Anda</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <Link v-for="rec in rekomendasi" :key="rec.id" :href="`/app/buku/${rec.id}`">
                    <Card class="hover:shadow-md transition-shadow cursor-pointer overflow-hidden">
                        <div class="aspect-[2/3] bg-muted flex items-center justify-center overflow-hidden">
                            <img v-if="rec.sampul" :src="rec.sampul" :alt="rec.judul" class="w-full h-full object-cover" />
                            <BookOpen v-else class="h-8 w-8 text-muted-foreground/50" />
                        </div>
                        <CardContent class="p-3 text-center">
                            <p class="font-medium text-sm truncate">{{ rec.judul }}</p>
                            <p class="text-xs text-muted-foreground">{{ rec.penulis?.nama }}</p>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </div>
</template>
