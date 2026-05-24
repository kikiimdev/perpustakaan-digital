<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, User } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Buku, Penulis } from '@/types';

defineProps<{
    penulis: Penulis;
    buku: {
        data: Buku[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();
</script>

<template>
    <Head :title="`Penulis: ${penulis.nama}`" />

    <div class="p-6 max-w-3xl space-y-6">
        <Link href="/admin/penulis">
            <Button variant="outline" size="sm">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Kembali
            </Button>
        </Link>

        <div class="flex items-start gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-muted">
                <User class="h-8 w-8 text-muted-foreground" />
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-semibold">{{ penulis.nama }}</h1>
                <p v-if="penulis.biografi" class="mt-2 text-muted-foreground leading-relaxed">
                    {{ penulis.biografi }}
                </p>
                <p v-else class="mt-2 text-sm text-muted-foreground italic">
                    Belum ada biografi.
                </p>
            </div>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <BookOpen class="h-4 w-4" />
                    Buku Karya {{ penulis.nama }}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div v-if="buku.data.length" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <Link
                        v-for="b in buku.data"
                        :key="b.id"
                        :href="`/admin/buku/${b.id}`"
                        class="block rounded border p-3 hover:bg-muted/50 transition-colors"
                    >
                        <p class="font-medium text-sm truncate">{{ b.judul }}</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            <span
                                v-for="k in b.kategori"
                                :key="k.id"
                                class="text-[10px] px-1.5 py-0.5 rounded bg-muted"
                            >
                                {{ k.nama }}
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-1">{{ b.jumlah_halaman }} hlm</p>
                    </Link>
                </div>
                <p v-else class="text-sm text-muted-foreground py-4 text-center">
                    Belum ada buku.
                </p>
            </CardContent>
        </Card>

        <div v-if="buku.last_page > 1" class="flex items-center justify-center gap-2">
            <template v-for="link in buku.links" :key="link.label">
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
