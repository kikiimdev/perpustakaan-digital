<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import type { User } from '@/types';

defineProps<{
    anggota: User;
}>();
</script>

<template>
    <Head title="Cetak Kartu Anggota" />

    <div id="kartu-cetak" class="p-8 max-w-md mx-auto">
        <div class="border-2 rounded-lg p-6 text-center space-y-4 bg-white print:shadow-none">
            <h2 class="text-lg font-bold uppercase">Kartu Anggota Perpustakaan</h2>

            <div class="space-y-2 text-left">
                <div>
                    <span class="text-sm text-muted-foreground">Nama</span>
                    <p class="font-medium">{{ anggota.name }}</p>
                </div>
                <div>
                    <span class="text-sm text-muted-foreground">Email</span>
                    <p class="font-medium">{{ anggota.email }}</p>
                </div>
                <div>
                    <span class="text-sm text-muted-foreground">No. Anggota</span>
                    <p class="font-medium">{{ String(anggota.id).padStart(6, '0') }}</p>
                </div>
            </div>

            <div class="border-t pt-4 text-xs text-muted-foreground">
                Dicetak pada {{ new Date().toLocaleDateString('id-ID') }}
            </div>
        </div>

        <div class="mt-6 text-center print:hidden">
            <button
                class="px-4 py-2 bg-primary text-primary-foreground rounded text-sm"
                onclick="window.print()"
            >
                Cetak Kartu
            </button>
        </div>
    </div>
</template>

<style scoped>
@media print {
    body {
        background: white;
    }
}
</style>
