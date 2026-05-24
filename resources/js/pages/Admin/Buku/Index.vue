<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Buku } from '@/types';

const props = defineProps<{
    buku: {
        data: Buku[];
        current_page: number;
        last_page: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    cari: string;
}>();

const deleteForm = useForm({});
const cari = ref(props.cari);

let timeout: ReturnType<typeof setTimeout>;
watch(cari, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/admin/buku', { cari: val || undefined }, { preserveState: true, replace: true });
    }, 300);
});
</script>

<template>
    <Head title="Manajemen Buku" />

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Manajemen Buku</h1>
            <Link href="/admin/buku/create">
                <Button>Tambah Buku</Button>
            </Link>
        </div>

        <div class="relative max-w-sm">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input v-model="cari" placeholder="Cari judul buku..." class="pl-9" />
        </div>

        <Card>
            <CardContent class="p-0">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Judul</TableHead>
                            <TableHead>Penulis</TableHead>
                            <TableHead>Kategori</TableHead>
                            <TableHead>Halaman</TableHead>
                            <TableHead class="w-32">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="item in buku.data" :key="item.id">
                            <TableCell class="font-medium">{{ item.judul }}</TableCell>
                            <TableCell>{{ item.penulis?.nama }}</TableCell>
                            <TableCell>
                                <span
                                    v-for="k in item.kategori"
                                    :key="k.id"
                                    class="mr-1 text-xs bg-muted px-2 py-0.5 rounded"
                                >
                                    {{ k.nama }}
                                </span>
                            </TableCell>
                            <TableCell>{{ item.jumlah_halaman }}</TableCell>
                            <TableCell class="space-x-2">
                                <Link :href="`/admin/buku/${item.id}`">
                                    <Button variant="ghost" size="sm">Detail</Button>
                                </Link>
                                <Link :href="`/admin/buku/${item.id}/edit`">
                                    <Button variant="outline" size="sm">Edit</Button>
                                </Link>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    :disabled="deleteForm.processing"
                                    @click="deleteForm.delete(`/admin/buku/${item.id}`)"
                                >
                                    Hapus
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="buku.data.length === 0">
                            <TableCell colspan="5" class="text-center text-muted-foreground">
                                Belum ada buku.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
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
