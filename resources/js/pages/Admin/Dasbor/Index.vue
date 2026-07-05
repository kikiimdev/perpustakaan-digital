<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    ArcElement,
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { LayoutDashboard } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Bar, Doughnut, Line } from 'vue-chartjs';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type {
    BukuTerbanyakDibaca,
    KategoriTerbanyakDibaca,
    RiwayatBacaPerGender,
    RiwayatBacaPerHari,
} from '@/types';

ChartJS.register(CategoryScale, LinearScale, BarElement, ArcElement, LineElement, PointElement, Title, Tooltip, Legend);

const props = defineProps<{
    bulanAwal: string;
    bulanAkhir: string;
    riwayatBacaPerHari: RiwayatBacaPerHari[];
    bukuTerbanyakDibaca: BukuTerbanyakDibaca[];
    riwayatBacaPerGender: RiwayatBacaPerGender[];
    kategoriTerbanyakDibaca: KategoriTerbanyakDibaca[];
}>();

const bulanAwal = ref(props.bulanAwal);
const bulanAkhir = ref(props.bulanAkhir);

let timeout: ReturnType<typeof setTimeout>;
const fetchData = () => {
    router.get(
        '/admin/dasbor',
        {
            bulan_awal: bulanAwal.value,
            bulan_akhir: bulanAkhir.value,
        },
        { preserveState: true, replace: true },
    );
};

watch([bulanAwal, bulanAkhir], () => {
    clearTimeout(timeout);
    timeout = setTimeout(fetchData, 300);
});

const formatPeriode = (val: string) => {
    const [y, m] = val.split('-');
    const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${months[Number(m)]} ${y}`;
};

const labelPeriode = computed(() => `${formatPeriode(bulanAwal.value)} — ${formatPeriode(bulanAkhir.value)}`);

const formatTanggal = (dateStr: string) => {
    const d = new Date(dateStr);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return `${d.getDate()} ${months[d.getMonth()]}`;
};

// Chart 0: Riwayat Baca per Hari
const chartHarianData = computed(() => {
    return {
        labels: props.riwayatBacaPerHari.map((i) => formatTanggal(i.tanggal)),
        datasets: [
            {
                label: 'Sesi Baca',
                data: props.riwayatBacaPerHari.map((i) => i.total),
                borderColor: '#3B82F6', // blue-500
                backgroundColor: '#3B82F6',
                fill: false,
                tension: 0.1,
            },
        ],
    };
});
const chartHarianOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
            },
        },
    },
};

// Chart 1: Buku Terbanyak Dibaca
const chartBukuData = computed(() => {
    return {
        labels: props.bukuTerbanyakDibaca.map((i) => (i.judul.length > 30 ? i.judul.substring(0, 30) + '...' : i.judul)),
        datasets: [
            {
                label: 'Total Halaman Dibaca',
                data: props.bukuTerbanyakDibaca.map((i) => i.total_halaman),
                backgroundColor: '#6366F1', // indigo-500
            },
        ],
    };
});
const chartBukuOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
    },
};

// Chart 2: Riwayat Baca per Gender
const chartGenderData = computed(() => {
    const labelMap: Record<string, string> = {
        'laki-laki': 'Laki-laki',
        perempuan: 'Perempuan',
        tidak_diketahui: 'Tidak Diketahui',
    };

    const bgMap: Record<string, string> = {
        'laki-laki': '#3B82F6', // blue-500
        perempuan: '#EC4899', // pink-500
        tidak_diketahui: '#9CA3AF', // gray-400
    };

    return {
        labels: props.riwayatBacaPerGender.map((i) => labelMap[i.gender] || i.gender),
        datasets: [
            {
                data: props.riwayatBacaPerGender.map((i) => i.total),
                backgroundColor: props.riwayatBacaPerGender.map((i) => bgMap[i.gender] || '#000000'),
            },
        ],
    };
});
const chartGenderOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' as const },
    },
};

// Chart 3: Kategori Terbanyak Dibaca
const chartKategoriData = computed(() => {
    return {
        labels: props.kategoriTerbanyakDibaca.map((i) => i.nama),
        datasets: [
            {
                label: 'Total Sesi Baca',
                data: props.kategoriTerbanyakDibaca.map((i) => i.total_dibaca),
                backgroundColor: '#10B981', // emerald-500
            },
        ],
    };
});
const chartKategoriOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
    },
};
</script>

<template>
    <Head title="Dasbor Admin" />

    <div class="space-y-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Dasbor Admin</h1>
                <p class="text-sm text-muted-foreground">{{ labelPeriode }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-end gap-4">
            <div class="space-y-1">
                <Label>Bulan Awal</Label>
                <Input type="month" v-model="bulanAwal" class="w-44" />
            </div>
            <div class="space-y-1">
                <Label>Bulan Akhir</Label>
                <Input type="month" v-model="bulanAkhir" class="w-44" />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-3">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <LayoutDashboard class="size-4" />
                            Riwayat Baca per Hari
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="riwayatBacaPerHari.length" class="h-72">
                            <Line :data="chartHarianData" :options="chartHarianOptions" />
                        </div>
                        <div v-else class="flex h-72 items-center justify-center text-muted-foreground">
                            Belum ada data untuk periode ini
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-2">
                <Card class="h-full">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <LayoutDashboard class="size-4" />
                            Buku Terbanyak Dibaca
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="bukuTerbanyakDibaca.length" class="h-72">
                            <Bar :data="chartBukuData" :options="chartBukuOptions" />
                        </div>
                        <div v-else class="flex h-72 items-center justify-center text-muted-foreground">
                            Belum ada data untuk periode ini
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-1">
                <Card class="h-full">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <LayoutDashboard class="size-4" />
                            Riwayat Baca per Gender
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="riwayatBacaPerGender.length" class="h-72 pb-4">
                            <Doughnut :data="chartGenderData" :options="chartGenderOptions" />
                        </div>
                        <div v-else class="flex h-72 items-center justify-center text-muted-foreground">
                            Belum ada data untuk periode ini
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-3">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <LayoutDashboard class="size-4" />
                            Kategori Terbanyak Dibaca
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="kategoriTerbanyakDibaca.length" class="h-72">
                            <Bar :data="chartKategoriData" :options="chartKategoriOptions" />
                        </div>
                        <div v-else class="flex h-72 items-center justify-center text-muted-foreground">
                            Belum ada data untuk periode ini
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
