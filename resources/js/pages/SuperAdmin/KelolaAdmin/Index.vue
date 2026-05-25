<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import type { User } from '@/types';

defineProps<{
    admins: User[];
}>();

const deleteForm = useForm({});
</script>

<template>
    <Head title="Kelola Admin" />

    <div class="space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Daftar Admin</h1>
            <Link href="/super-admin/kelola-admin/create">
                <Button>Tambah Admin</Button>
            </Link>
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
                        <TableRow v-for="admin in admins" :key="admin.id">
                            <TableCell>{{ admin.name }}</TableCell>
                            <TableCell>{{ admin.email }}</TableCell>
                            <TableCell class="space-x-2">
                                <Link
                                    :href="`/super-admin/kelola-admin/${admin.id}/edit`"
                                >
                                    <Button variant="outline" size="sm"
                                        >Edit</Button
                                    >
                                </Link>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    :disabled="deleteForm.processing"
                                    @click="
                                        deleteForm.delete(
                                            `/super-admin/kelola-admin/${admin.id}`,
                                        )
                                    "
                                >
                                    Hapus
                                </Button>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="admins.length === 0">
                            <TableCell
                                colspan="3"
                                class="text-center text-muted-foreground"
                            >
                                Belum ada admin.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </div>
</template>
