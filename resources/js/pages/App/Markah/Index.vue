<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Bookmark } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { MarkahBuku } from '@/types';

defineProps<{
    markah: {
        data: MarkahBuku[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();
</script>

<template>
    <Head title="Markah Buku" />

    <div class="space-y-6 p-6">
        <h1 class="text-2xl font-semibold">Markah Buku Saya</h1>

        <div
            v-if="markah.data.length"
            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4"
        >
            <Link
                v-for="m in markah.data"
                :key="m.id"
                :href="`/app/baca/${m.buku_id}?halaman=${m.halaman}`"
            >
                <Card class="cursor-pointer transition-shadow hover:shadow-md overflow-hidden">
                    <div class="aspect-[2/3] bg-muted flex items-center justify-center overflow-hidden">
                        <img v-if="m.buku?.sampul" :src="m.buku.sampul" :alt="m.buku?.judul" class="w-full h-full object-cover" />
                        <BookOpen v-else class="h-8 w-8 text-muted-foreground/50" />
                    </div>
                    <CardHeader>
                        <CardTitle class="text-base">{{
                            m.buku?.judul
                        }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">
                            {{ m.buku?.penulis?.nama }}
                        </p>
                        <div class="mt-2 flex items-center gap-2">
                            <Bookmark class="h-4 w-4 text-primary" />
                            <span class="text-sm font-medium"
                                >Halaman {{ m.halaman }}</span
                            >
                        </div>
                        <p
                            v-if="m.catatan"
                            class="mt-2 line-clamp-2 text-xs text-muted-foreground"
                        >
                            "{{ m.catatan }}"
                        </p>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <div v-else class="py-12 text-center text-muted-foreground">
            <Bookmark class="mx-auto mb-4 h-12 w-12 text-muted-foreground/50" />
            <p>Belum ada markah buku.</p>
        </div>

        <div
            v-if="markah.last_page > 1"
            class="flex items-center justify-center gap-2"
        >
            <template v-for="link in markah.links" :key="link.label">
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
