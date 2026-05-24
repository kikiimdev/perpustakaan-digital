<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Bookmark } from 'lucide-vue-next';
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

    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">Markah Buku Saya</h1>

        <div v-if="markah.data.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <Link v-for="m in markah.data" :key="m.id" :href="`/app/baca/${m.buku_id}?halaman=${m.halaman}`">
                <Card class="hover:shadow-md transition-shadow cursor-pointer">
                    <CardHeader>
                        <CardTitle class="text-base">{{ m.buku?.judul }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">{{ m.buku?.penulis?.nama }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <Bookmark class="h-4 w-4 text-primary" />
                            <span class="text-sm font-medium">Halaman {{ m.halaman }}</span>
                        </div>
                        <p v-if="m.catatan" class="text-xs text-muted-foreground mt-2 line-clamp-2">
                            "{{ m.catatan }}"
                        </p>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <div v-else class="text-center text-muted-foreground py-12">
            <Bookmark class="h-12 w-12 mx-auto mb-4 text-muted-foreground/50" />
            <p>Belum ada markah buku.</p>
        </div>

        <div v-if="markah.last_page > 1" class="flex items-center justify-center gap-2">
            <template v-for="link in markah.links" :key="link.label">
                <Button
                    v-if="link.url"
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                    as-child
                >
                    <Link :href="link.url" v-html="link.label" />
                </Button>
            </template>
        </div>
    </div>
</template>
