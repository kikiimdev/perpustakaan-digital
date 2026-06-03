<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Bookmark, Heart } from 'lucide-vue-next';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { Buku, MarkahBuku } from '@/types';

const props = defineProps<{
    buku: Buku;
    isFavorit: boolean;
    markah: MarkahBuku[];
}>();

const isFavorit = ref(props.isFavorit);
const loading = ref(false);

const csrfToken = () => {
    const el = document.querySelector('meta[name="csrf-token"]');

    return el?.getAttribute('content') ?? '';
};

const toggleFavorit = async () => {
    loading.value = true;
    const res = await fetch('/app/favorit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({ buku_id: props.buku.id }),
    });
    const data = await res.json();
    isFavorit.value = data.status === 'added';
    loading.value = false;
};
</script>

<template>
    <Head :title="buku.judul" />

    <div class="p-6 max-w-3xl space-y-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ buku.judul }}</h1>
                <p class="text-muted-foreground">
                    Oleh <strong>{{ buku.penulis?.nama }}</strong>
                </p>
            </div>
            <Button
                :variant="isFavorit ? 'default' : 'outline'"
                :disabled="loading"
                @click="toggleFavorit"
            >
                <Heart class="mr-2 h-4 w-4" :class="{ 'fill-current': isFavorit }" />
                {{ isFavorit ? 'Favorit' : 'Tambah Favorit' }}
            </Button>
        </div>

        <Card>
            <CardHeader><CardTitle>Sinopsis</CardTitle></CardHeader>
            <CardContent>
                <p class="text-sm leading-relaxed whitespace-pre-line">{{ buku.sinopsis }}</p>
            </CardContent>
        </Card>

        <div class="flex flex-wrap gap-2">
            <span
                v-for="k in buku.kategori"
                :key="k.id"
                class="px-3 py-1 text-xs rounded bg-muted"
            >
                {{ k.nama }}
            </span>
        </div>

        <div class="flex items-center gap-4 text-sm text-muted-foreground">
            <span>{{ buku.jumlah_halaman }} halaman</span>
        </div>

        <Link :href="`/app/baca/${buku.id}`">
            <Button size="lg">Baca Buku</Button>
        </Link>

        <div v-if="markah.length" class="space-y-2">
            <h2 class="text-lg font-semibold">Markah Saya</h2>
            <div class="space-y-2">
                <Link 
                    v-for="m in markah" 
                    :key="m.id" 
                    :href="`/app/baca/${buku.id}?halaman=${m.halaman}`"
                >
                    <Card class="cursor-pointer transition-shadow hover:shadow-md hover:bg-muted/50">
                        <CardContent class="p-3 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <Bookmark class="h-4 w-4 text-primary" />
                                <span class="font-medium">Halaman {{ m.halaman }}</span>
                            </div>
                            <span v-if="m.catatan" class="text-sm text-muted-foreground line-clamp-1 max-w-[200px]">
                                "{{ m.catatan }}"
                            </span>
                        </CardContent>
                    </Card>
                </Link>
            </div>
        </div>
    </div>
</template>
