<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Heart } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { BukuFavorit } from '@/types';

defineProps<{
    favorit: {
        data: BukuFavorit[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();
</script>

<template>
    <Head title="Buku Favorit" />

    <div class="space-y-6 p-6">
        <h1 class="text-2xl font-semibold">Buku Favorit Saya</h1>

        <div
            v-if="favorit.data.length"
            class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
        >
            <Link
                v-for="fav in favorit.data"
                :key="fav.id"
                :href="`/app/buku/${fav.buku_id}`"
            >
                <Card class="cursor-pointer transition-shadow hover:shadow-md">
                    <CardHeader>
                        <CardTitle class="text-base">{{
                            fav.buku?.judul
                        }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            {{ fav.buku?.penulis?.nama }}
                        </p>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <div v-else class="py-12 text-center text-muted-foreground">
            <Heart class="mx-auto mb-4 h-12 w-12 text-muted-foreground/50" />
            <p>Belum ada buku favorit.</p>
        </div>

        <div
            v-if="favorit.last_page > 1"
            class="flex items-center justify-center gap-2"
        >
            <template v-for="link in favorit.links" :key="link.label">
                <Button
                    v-if="link.url"
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                    as-child
                >
                    <Link :href="link.url">{{ link.label }}</Link>
                </Button>
            </template>
        </div>
    </div>
</template>
