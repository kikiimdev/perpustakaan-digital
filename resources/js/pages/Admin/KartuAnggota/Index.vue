<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { User } from '@/types';

const props = defineProps<{
    users: {
        data: User[];
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
        router.get('/admin/kartu-anggota', { cari: val || undefined }, { preserveState: true, replace: true });
    }, 300);
});
</script>

<template>
    <Head title="Kartu Anggota" />

    <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">Cetak Kartu Anggota</h1>

        <div class="relative max-w-sm">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input v-model="cari" placeholder="Cari nama atau email..." class="pl-9" />
        </div>

        <Card>
            <CardContent class="p-0">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nama</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead class="w-32">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell>{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>
                                <Link :href="`/admin/kartu-anggota/cetak/${user.id}`">
                                    <Button variant="outline" size="sm">Cetak</Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="users.data.length === 0">
                            <TableCell colspan="3" class="text-center text-muted-foreground">
                                Belum ada anggota.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <div v-if="users.last_page > 1" class="flex items-center justify-center gap-2">
            <template v-for="link in users.links" :key="link.label">
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
