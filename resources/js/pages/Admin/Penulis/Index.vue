<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { BookOpen, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Penulis } from '@/types';

const props = defineProps<{
    penulis: {
        data: (Penulis & { buku_count: number })[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    cari: string;
}>();

const cari = ref(props.cari);

let timeout: ReturnType<typeof setTimeout>;
watch(cari, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/admin/penulis', { cari: val || undefined }, { preserveState: true, replace: true });
    }, 300);
});
</script>

<template>
    <Head title="Daftar Penulis" />

    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">Daftar Penulis</h1>

        <div class="relative max-w-sm">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input v-model="cari" placeholder="Cari nama penulis..." class="pl-9" />
        </div>

        <Card>
            <CardContent class="p-0">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nama</TableHead>
                            <TableHead>Biografi</TableHead>
                            <TableHead class="text-right">Jumlah Buku</TableHead>
                            <TableHead class="w-24"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="p in penulis.data" :key="p.id">
                            <TableCell class="font-medium">{{ p.nama }}</TableCell>
                            <TableCell class="text-muted-foreground text-sm max-w-xs truncate">
                                {{ p.biografi || '-' }}
                            </TableCell>
                            <TableCell class="text-right">{{ p.buku_count }}</TableCell>
                            <TableCell>
                                <Link :href="`/admin/penulis/${p.id}`">
                                    <Button variant="ghost" size="sm">
                                        <BookOpen class="mr-1 h-4 w-4" />
                                        Detail
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="penulis.data.length === 0">
                            <TableCell colspan="4" class="text-center text-muted-foreground">
                                Belum ada penulis.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <div v-if="penulis.last_page > 1" class="flex items-center justify-center gap-2">
            <template v-for="link in penulis.links" :key="link.label">
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
