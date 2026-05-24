<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { BookOpen, Download, FileText, Heart, Library } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import type { Buku, BukuTerbanyakDibaca, BukuTerfavorit } from '@/types';

const props = defineProps<{
    bulanAwal: string;
    bulanAkhir: string;
    bukuDitambahkan: number;
    bukuTerbanyakDibaca: BukuTerbanyakDibaca[];
    bukuTerfavorit: BukuTerfavorit[];
    aktivitas: {
        total_pembaca: number;
        total_sesi: number;
        total_halaman: number;
        buku_dibaca: number;
        total_pengguna: number;
        total_buku: number;
    };
    bukuBaruDaftar: Buku[];
}>();

const bulanAwal = ref(props.bulanAwal);
const bulanAkhir = ref(props.bulanAkhir);

let timeout: ReturnType<typeof setTimeout>;
const fetchData = () => {
    router.get('/admin/laporan', {
        bulan_awal: bulanAwal.value,
        bulan_akhir: bulanAkhir.value,
    }, { preserveState: true, replace: true });
};
watch([bulanAwal, bulanAkhir], () => {
    clearTimeout(timeout);
    timeout = setTimeout(fetchData, 300);
});

const exportPdf = (jenis: string) => {
    window.open(`/admin/laporan/${jenis}?bulan_awal=${bulanAwal.value}&bulan_akhir=${bulanAkhir.value}`, '_blank');
};

const formatPeriode = (val: string) => {
    const [y, m] = val.split('-');
    const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${months[Number(m)]} ${y}`;
};

const labelPeriode = computed(() => `${formatPeriode(bulanAwal.value)} — ${formatPeriode(bulanAkhir.value)}`);
</script>

<template>
    <Head title="Laporan Perpustakaan" />

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Laporan Perpustakaan</h1>
                <p class="text-sm text-muted-foreground">{{ labelPeriode }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 items-end">
            <div class="space-y-1">
                <Label>Bulan Awal</Label>
                <Input type="month" v-model="bulanAwal" class="w-44" />
            </div>
            <div class="space-y-1">
                <Label>Bulan Akhir</Label>
                <Input type="month" v-model="bulanAkhir" class="w-44" />
            </div>
        </div>

        <!-- 1. Buku Ditambahkan -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle class="flex items-center gap-2">
                    <FileText class="h-4 w-4" />
                    Buku Ditambahkan
                </CardTitle>
                <Button variant="outline" size="sm" @click="exportPdf('cetak-buku-ditambahkan')">
                    <Download class="mr-1 h-3 w-3" />
                    PDF
                </Button>
            </CardHeader>
            <CardContent>
                <p class="text-3xl font-bold mb-4">{{ bukuDitambahkan }} buku</p>

                <Table v-if="bukuBaruDaftar.length">
                    <TableHeader>
                        <TableRow>
                            <TableHead>Judul</TableHead>
                            <TableHead>Penulis</TableHead>
                            <TableHead>Halaman</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="b in bukuBaruDaftar.slice(0, 5)" :key="b.id">
                            <TableCell class="font-medium">{{ b.judul }}</TableCell>
                            <TableCell>{{ b.penulis?.nama }}</TableCell>
                            <TableCell>{{ b.jumlah_halaman }}</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <p v-else class="text-sm text-muted-foreground">Belum ada buku ditambahkan.</p>
            </CardContent>
        </Card>

        <!-- 2. Buku Terbanyak Dibaca -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle class="flex items-center gap-2">
                    <BookOpen class="h-4 w-4" />
                    Buku Terbanyak Dibaca
                </CardTitle>
                <Button variant="outline" size="sm" @click="exportPdf('cetak-buku-terbanyak-dibaca')">
                    <Download class="mr-1 h-3 w-3" />
                    PDF
                </Button>
            </CardHeader>
            <CardContent class="p-0">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Judul</TableHead>
                            <TableHead class="text-right">Total Halaman</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="buku in bukuTerbanyakDibaca" :key="buku.buku_id">
                            <TableCell>{{ buku.judul }}</TableCell>
                            <TableCell class="text-right">{{ buku.total_halaman }}</TableCell>
                        </TableRow>
                        <TableRow v-if="bukuTerbanyakDibaca.length === 0">
                            <TableCell colspan="2" class="text-center text-muted-foreground">
                                Belum ada data.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <!-- 3. Buku Terfavorit -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle class="flex items-center gap-2">
                    <Heart class="h-4 w-4" />
                    Buku Terfavorit
                </CardTitle>
                <Button variant="outline" size="sm" @click="exportPdf('cetak-buku-terfavorit')">
                    <Download class="mr-1 h-3 w-3" />
                    PDF
                </Button>
            </CardHeader>
            <CardContent class="p-0">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Judul</TableHead>
                            <TableHead class="text-right">Total Favorit</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="buku in bukuTerfavorit" :key="buku.buku_id">
                            <TableCell>{{ buku.judul }}</TableCell>
                            <TableCell class="text-right">{{ buku.total_favorit }}</TableCell>
                        </TableRow>
                        <TableRow v-if="bukuTerfavorit.length === 0">
                            <TableCell colspan="2" class="text-center text-muted-foreground">
                                Belum ada data.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <!-- 4. Aktivitas Membaca -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle class="flex items-center gap-2">
                    <Library class="h-4 w-4" />
                    Aktivitas Membaca
                </CardTitle>
                <Button variant="outline" size="sm" @click="exportPdf('cetak-aktivitas-membaca')">
                    <Download class="mr-1 h-3 w-3" />
                    PDF
                </Button>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <p class="text-2xl font-bold">{{ aktivitas.total_pembaca }}</p>
                        <p class="text-xs text-muted-foreground">Pembaca Aktif</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ aktivitas.total_sesi }}</p>
                        <p class="text-xs text-muted-foreground">Sesi Baca</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ aktivitas.total_halaman.toLocaleString() }}</p>
                        <p class="text-xs text-muted-foreground">Halaman Dibaca</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ aktivitas.buku_dibaca }}</p>
                        <p class="text-xs text-muted-foreground">Buku Dibaca</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm text-muted-foreground">
                    <span>Total Pengguna: <strong>{{ aktivitas.total_pengguna }}</strong></span>
                    <span>Total Buku: <strong>{{ aktivitas.total_buku }}</strong></span>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
