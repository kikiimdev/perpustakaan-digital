<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Heart, Eye } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Buku } from '@/types';

const props = defineProps<{
    buku: Buku;
    stats: {
        readsThisMonth: number;
        favoritesThisMonth: number;
        totalReads: number;
        totalFavorites: number;
    };
}>();
</script>

<template>
    <Head :title="buku.judul" />

    <div class="min-h-screen bg-gradient-to-b from-background to-muted/30">
        <div class="mx-auto max-w-4xl p-6 space-y-6">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                <div class="flex-shrink-0">
                    <img 
                        v-if="buku.sampul" 
                        :src="buku.sampul" 
                        :alt="buku.judul" 
                        class="h-64 w-48 rounded-lg border object-cover shadow-sm"
                    />
                    <div v-else class="flex h-64 w-48 items-center justify-center rounded-lg border bg-muted shadow-sm">
                        <BookOpen class="h-12 w-12 text-muted-foreground" />
                    </div>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold tracking-tight">{{ buku.judul }}</h1>
                    <p class="mt-1 text-muted-foreground">
                        Oleh <strong>{{ buku.penulis?.nama }}</strong>
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardContent class="p-4 flex flex-col items-center justify-center text-center">
                        <Eye class="mb-2 h-6 w-6 text-primary" />
                        <span class="text-2xl font-bold">{{ stats.readsThisMonth }}</span>
                        <span class="text-xs text-muted-foreground">Dibaca bulan ini</span>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 flex flex-col items-center justify-center text-center">
                        <Heart class="mb-2 h-6 w-6 text-primary" />
                        <span class="text-2xl font-bold">{{ stats.favoritesThisMonth }}</span>
                        <span class="text-xs text-muted-foreground">Favorit bulan ini</span>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 flex flex-col items-center justify-center text-center">
                        <Eye class="mb-2 h-6 w-6 text-muted-foreground" />
                        <span class="text-2xl font-bold">{{ stats.totalReads }}</span>
                        <span class="text-xs text-muted-foreground">Total dibaca</span>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="p-4 flex flex-col items-center justify-center text-center">
                        <Heart class="mb-2 h-6 w-6 text-muted-foreground" />
                        <span class="text-2xl font-bold">{{ stats.totalFavorites }}</span>
                        <span class="text-xs text-muted-foreground">Total favorit</span>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader><CardTitle>Sinopsis</CardTitle></CardHeader>
                <CardContent>
                    <p class="text-sm leading-relaxed whitespace-pre-line">{{ buku.sinopsis || 'Belum ada sinopsis.' }}</p>
                </CardContent>
            </Card>

            <div class="flex flex-wrap gap-2">
                <span
                    v-for="k in buku.kategori"
                    :key="k.id"
                    class="px-3 py-1 text-xs rounded-full bg-primary/10 text-primary font-medium"
                >
                    {{ k.nama }}
                </span>
            </div>

            <div class="flex items-center gap-4 text-sm text-muted-foreground">
                <span>{{ buku.jumlah_halaman }} halaman</span>
            </div>

            <div class="pt-4">
                <Link :href="`/app/baca/${buku.id}`">
                    <Button size="lg" class="w-full sm:w-auto">
                        <BookOpen class="mr-2 h-5 w-5" />
                        Baca Buku Sekarang
                    </Button>
                </Link>
                <p class="mt-3 text-xs text-muted-foreground text-center sm:text-left">
                    * Anda perlu masuk atau mendaftar terlebih dahulu untuk membaca buku ini.
                </p>
            </div>
        </div>
    </div>
</template>
