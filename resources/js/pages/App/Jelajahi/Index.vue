<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { BookOpen, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { Buku, Kategori } from '@/types';

const props = defineProps<{
    buku: {
        data: Buku[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    kategori: Kategori[];
    cari: string;
    kategoriTerpilih: number | null;
}>();

const cari = ref(props.cari);
const kategoriAktif = ref(props.kategoriTerpilih);

let timeout: ReturnType<typeof setTimeout>;
function filter() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            '/app/jelajahi',
            {
                cari: cari.value || undefined,
                kategori: kategoriAktif.value ?? undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 300);
}

watch(cari, filter);

function pilihKategori(id: number | null) {
    kategoriAktif.value = id;
    filter();
}
</script>

<template>
    <Head title="Jelajahi Buku" />

    <div class="space-y-6 p-6">
        <h1 class="text-2xl font-semibold">Jelajahi Buku</h1>

        <div class="flex flex-col gap-4 sm:flex-row">
            <div class="relative max-w-sm flex-1">
                <Search
                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="cari"
                    placeholder="Cari judul buku..."
                    class="pl-9"
                />
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <Badge
                :variant="kategoriAktif === null ? 'default' : 'outline'"
                class="cursor-pointer"
                @click="pilihKategori(null)"
            >
                Semua
            </Badge>
            <Badge
                v-for="k in kategori"
                :key="k.id"
                :variant="kategoriAktif === k.id ? 'default' : 'outline'"
                class="cursor-pointer"
                @click="pilihKategori(k.id)"
            >
                {{ k.nama }}
            </Badge>
        </div>

        <div
            v-if="buku.data.length"
            class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
        >
            <Link
                v-for="b in buku.data"
                :key="b.id"
                :href="`/app/buku/${b.id}`"
            >
                <Card
                    class="h-full cursor-pointer transition-shadow hover:shadow-md"
                >
                    <CardHeader class="p-3">
                        <div
                            v-if="b.sampul"
                            class="mb-2 aspect-[2/3] overflow-hidden rounded bg-muted"
                        >
                            <img
                                :src="b.sampul"
                                :alt="b.judul"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div
                            v-else
                            class="mb-2 flex aspect-[2/3] items-center justify-center rounded bg-muted"
                        >
                            <BookOpen class="h-8 w-8 text-muted-foreground" />
                        </div>
                        <CardTitle class="line-clamp-2 text-sm">{{
                            b.judul
                        }}</CardTitle>
                    </CardHeader>
                    <CardContent class="p-3 pt-0">
                        <p class="text-xs text-muted-foreground">
                            {{ b.penulis?.nama }}
                        </p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            <Badge
                                v-for="k in b.kategori"
                                :key="k.id"
                                variant="secondary"
                                class="text-[10px]"
                            >
                                {{ k.nama }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <div v-else class="py-12 text-center text-muted-foreground">
            Tidak ada buku yang ditemukan.
        </div>

        <div
            v-if="buku.last_page > 1"
            class="flex items-center justify-center gap-2"
        >
            <template v-for="link in buku.links" :key="link.label">
                <Button
                    v-if="link.url"
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                    as-child
                >
                    <Link :href="link.url" v-html="link.label"></Link>
                </Button>
                <Button
                    v-else
                    variant="outline"
                    size="sm"
                    disabled
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
