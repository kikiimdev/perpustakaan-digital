<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Bookmark, CheckCheck, ChevronLeft, ChevronRight, ZoomIn, ZoomOut } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import VuePdfEmbed from 'vue-pdf-embed';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { Buku, MarkahBuku } from '@/types';

const props = defineProps<{
    buku: Buku;
    markah: MarkahBuku[];
}>();

const currentPage = ref(1);
const totalPages = ref(props.buku.jumlah_halaman);
const pdfSource = `/storage/${props.buku.file_pdf}`;
const visitedPages = new Set<number>();
const bookmarkStatus = ref<'added' | 'removed' | null>(null);
const scale = ref(1.0);

const bookmarkedPages = new Set(props.markah.map((m) => m.halaman));
const isBookmarked = computed(() => bookmarkedPages.has(currentPage.value));

const urlParams = new URLSearchParams(window.location.search);
const halamanParam = urlParams.get('halaman');
if (halamanParam) {
    const page = parseInt(halamanParam);
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
}

watch(currentPage, (page) => {
    visitedPages.add(page);
    bookmarkStatus.value = null;
    const url = new URL(window.location.href);
    url.searchParams.set('halaman', String(page));
    window.history.replaceState({}, '', url.toString());
});

const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

const zoomIn = () => {
    if (scale.value < 2.0) {
        scale.value = Math.round((scale.value + 0.25) * 100) / 100;
    }
};

const zoomOut = () => {
    if (scale.value > 0.5) {
        scale.value = Math.round((scale.value - 0.25) * 100) / 100;
    }
};

const csrfToken = () => {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el?.getAttribute('content') ?? '';
};

const handleSimpanMarkah = async () => {
    const res = await fetch('/app/markah', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({
            buku_id: props.buku.id,
            halaman: currentPage.value,
            catatan: '',
        }),
    });
    const data = await res.json();
    bookmarkStatus.value = data.status;
    if (data.status === 'added') {
        bookmarkedPages.add(currentPage.value);
    } else {
        bookmarkedPages.delete(currentPage.value);
    }
};

const handleCatatStatistik = async () => {
    if (visitedPages.size === 0) return;
    await fetch('/app/catat-halaman', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({
            buku_id: props.buku.id,
            halaman_dibaca: visitedPages.size,
            tanggal: new Date().toISOString().split('T')[0],
        }),
    });
    visitedPages.clear();
};

router.on('before', () => {
    handleCatatStatistik();
});

onBeforeUnmount(() => {
    handleCatatStatistik();
});

const progressPercent = computed(() =>
    totalPages.value > 0 ? Math.round((currentPage.value / totalPages.value) * 100) : 0
);
</script>

<template>
    <Head :title="`Baca: ${buku.judul}`" />

    <div class="flex h-[calc(100vh-4rem)] flex-col">
        <!-- Header -->
        <div class="flex shrink-0 items-center justify-between border-b bg-background p-4">
            <div class="flex items-center gap-3">
                <a :href="`/app/buku/${buku.id}`">
                    <Button variant="ghost" size="icon">
                        <ArrowLeft class="h-5 w-5" />
                    </Button>
                </a>
                <div>
                    <h1 class="max-w-md truncate font-medium">{{ buku.judul }}</h1>
                    <p class="text-sm text-muted-foreground">{{ buku.penulis?.nama }}</p>
                </div>
            </div>

            <Button
                :variant="isBookmarked && bookmarkStatus !== 'removed' ? 'default' : 'outline'"
                size="sm"
                @click="handleSimpanMarkah"
            >
                <Bookmark v-if="!isBookmarked || bookmarkStatus === 'removed'" class="mr-1 h-4 w-4" />
                <CheckCheck v-else class="mr-1 h-4 w-4" />
                {{ isBookmarked && bookmarkStatus !== 'removed' ? 'Halaman ' + currentPage + ' Ditandai' : 'Tandai Halaman ' + currentPage }}
            </Button>
        </div>

        <!-- Zoom bar -->
        <div class="flex justify-center gap-1 border-b bg-muted/50 px-4 py-1.5 shrink-0">
            <Button variant="ghost" size="icon" class="h-7 w-7" @click="zoomOut" :disabled="scale <= 0.5">
                <ZoomOut class="h-4 w-4" />
            </Button>
            <span class="flex w-12 items-center justify-center text-xs tabular-nums">
                {{ Math.round(scale * 100) }}%
            </span>
            <Button variant="ghost" size="icon" class="h-7 w-7" @click="zoomIn" :disabled="scale >= 2.0">
                <ZoomIn class="h-4 w-4" />
            </Button>
        </div>

        <!-- PDF -->
        <div class="flex flex-1 justify-center overflow-auto bg-muted/30 py-4">
            <div :style="{ transform: `scale(${scale})`, transformOrigin: 'top center' }" class="inline-block">
                <VuePdfEmbed :source="pdfSource" :page="currentPage" :scale="scale" class="shadow-lg" />
            </div>
        </div>

        <!-- Footer navigation -->
        <div class="flex shrink-0 items-center justify-between border-t bg-background p-3">
            <Button variant="outline" size="sm" :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">
                <ChevronLeft class="h-4 w-4" />
                Sebelumnya
            </Button>

            <div class="flex items-center gap-2">
                <Input v-model.number="currentPage" type="number" min="1" :max="totalPages" class="h-8 w-16 text-center" />
                <span class="text-sm text-muted-foreground">/ {{ totalPages }}</span>
                <span class="ml-2 text-xs text-muted-foreground">({{ progressPercent }}%)</span>
            </div>

            <Button variant="outline" size="sm" :disabled="currentPage >= totalPages" @click="goToPage(currentPage + 1)">
                Selanjutnya
                <ChevronRight class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>
