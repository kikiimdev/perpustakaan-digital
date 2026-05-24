<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, FileText, User } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Buku } from '@/types';

defineProps<{
    buku: Buku;
}>();
</script>

<template>
    <Head :title="`Detail: ${buku.judul}`" />

    <div class="p-6 max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <Link href="/admin/buku">
                <Button variant="outline" size="sm">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Kembali
                </Button>
            </Link>
            <Link :href="`/admin/buku/${buku.id}/edit`">
                <Button variant="outline" size="sm">Edit</Button>
            </Link>
        </div>

        <div class="flex gap-6">
            <div v-if="buku.sampul" class="shrink-0">
                <img
                    :src="`/storage/${buku.sampul}`"
                    :alt="buku.judul"
                    class="w-40 rounded object-cover shadow"
                />
            </div>
            <div v-else class="shrink-0 w-40 h-56 bg-muted rounded flex items-center justify-center">
                <BookOpen class="h-12 w-12 text-muted-foreground/40" />
            </div>

            <div class="flex-1 space-y-4">
                <div>
                    <h1 class="text-2xl font-semibold">{{ buku.judul }}</h1>
                    <p class="text-muted-foreground flex items-center gap-1 mt-1">
                        <User class="h-3.5 w-3.5" />
                        {{ buku.penulis?.nama }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="k in buku.kategori"
                        :key="k.id"
                        class="px-3 py-1 text-xs rounded bg-muted"
                    >
                        {{ k.nama }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-muted-foreground">Halaman</span>
                        <p class="font-medium">{{ buku.jumlah_halaman }}</p>
                    </div>
                    <div>
                        <span class="text-muted-foreground">File PDF</span>
                        <p class="font-medium text-xs truncate">{{ buku.file_pdf }}</p>
                    </div>
                </div>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <FileText class="h-4 w-4" />
                    Sinopsis
                </CardTitle>
            </CardHeader>
            <CardContent>
                <p class="text-sm leading-relaxed whitespace-pre-line">{{ buku.sinopsis }}</p>
            </CardContent>
        </Card>
    </div>
</template>
