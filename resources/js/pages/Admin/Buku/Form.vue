<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import InputError from '@/components/InputError.vue';
import type { Buku, Kategori, Penulis } from '@/types';

const props = defineProps<{
    buku?: Buku;
    penulis: Penulis[];
    kategori: Kategori[];
    selectedKategori?: number[];
}>();

const isEditing = !!props.buku;

const form = useForm({
    penulis_id: props.buku?.penulis_id ?? '',
    judul: props.buku?.judul ?? '',
    sinopsis: props.buku?.sinopsis ?? '',
    sampul: null as File | null,
    file_pdf: null as File | null,
    jumlah_halaman: props.buku?.jumlah_halaman ?? '',
    kategori_ids: props.selectedKategori ?? [] as (number | string)[],
});

const showPenulisModal = ref(false);
const namaPenulisBaru = ref('');
const errorPenulis = ref('');

const penulisList = ref<Penulis[]>([...props.penulis]);

const showKategoriModal = ref(false);
const namaKategoriBaru = ref('');
const errorKategori = ref('');

const kategoriOptions = ref<Kategori[]>([...props.kategori]);

const handleTambahPenulisCepat = async () => {
    const res = await fetch('/admin/penulis/cepat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
        },
        body: JSON.stringify({ nama: namaPenulisBaru.value }),
    });

    if (res.ok) {
        const newPenulis = await res.json();
        penulisList.value.push(newPenulis);
        form.penulis_id = newPenulis.id;
        showPenulisModal.value = false;
        namaPenulisBaru.value = '';
        errorPenulis.value = '';
    } else {
        const err = await res.json();
        errorPenulis.value = err.errors?.nama?.[0] ?? 'Terjadi kesalahan.';
    }
};

const handleTambahKategoriCepat = async () => {
    const res = await fetch('/admin/kategori/cepat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
        },
        body: JSON.stringify({ nama: namaKategoriBaru.value }),
    });

    if (res.ok) {
        const newKategori = await res.json();
        kategoriOptions.value.push(newKategori);
        form.kategori_ids.push(newKategori.id);
        showKategoriModal.value = false;
        namaKategoriBaru.value = '';
        errorKategori.value = '';
    } else {
        const err = await res.json();
        errorKategori.value = err.errors?.nama?.[0] ?? 'Terjadi kesalahan.';
    }
};

const handleSubmit = () => {
    if (isEditing) {
        form.post(`/admin/buku/${props.buku!.id}`, {
            forceFormData: true,
            headers: { 'X-HTTP-Method-Override': 'PUT' },
        });
    } else {
        form.post('/admin/buku', { forceFormData: true });
    }
};

const toggleKategori = (id: number) => {
    const idx = form.kategori_ids.indexOf(id);
    if (idx >= 0) {
        form.kategori_ids.splice(idx, 1);
    } else {
        (form.kategori_ids as number[]).push(id);
    }
};

</script>

<template>
    <Head :title="isEditing ? 'Edit Buku' : 'Tambah Buku'" />

    <div class="p-6 max-w-2xl space-y-6">
        <h1 class="text-2xl font-semibold">
            {{ isEditing ? 'Edit Buku' : 'Tambah Buku' }}
        </h1>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <Card>
                <CardHeader><CardTitle>Informasi Buku</CardTitle></CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-2">
                        <Label for="judul">Judul</Label>
                        <Input id="judul" v-model="form.judul" required />
                        <InputError :message="form.errors.judul" />
                    </div>

                    <div class="space-y-2">
                        <Label>Penulis</Label>
                        <div class="flex gap-2">
                            <Select v-model="form.penulis_id">
                                <SelectTrigger class="flex-1">
                                    <SelectValue placeholder="Pilih penulis" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="p in penulisList"
                                        :key="p.id"
                                        :value="String(p.id)"
                                    >
                                        {{ p.nama }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Dialog v-model:open="showPenulisModal">
                                <DialogTrigger as-child>
                                    <Button type="button" variant="outline">+ Penulis Baru</Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Tambah Penulis Baru</DialogTitle>
                                    </DialogHeader>
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <Label for="penulis-nama">Nama Penulis</Label>
                                            <Input
                                                id="penulis-nama"
                                                v-model="namaPenulisBaru"
                                                required
                                            />
                                            <InputError v-if="errorPenulis" :message="errorPenulis" />
                                        </div>
                                        <Button
                                            type="button"
                                            @click="handleTambahPenulisCepat"
                                        >
                                            Simpan
                                        </Button>
                                    </div>
                                </DialogContent>
                            </Dialog>
                        </div>
                        <InputError :message="form.errors.penulis_id" />
                    </div>

                    <div class="space-y-2">
                        <Label for="sinopsis">Sinopsis</Label>
                        <Textarea id="sinopsis" v-model="form.sinopsis" rows="4" required />
                        <InputError :message="form.errors.sinopsis" />
                    </div>

                    <div class="space-y-2">
                        <Label for="jumlah_halaman">Jumlah Halaman</Label>
                        <Input id="jumlah_halaman" v-model="form.jumlah_halaman" type="number" min="1" required />
                        <InputError :message="form.errors.jumlah_halaman" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center justify-between">
                        Kategori
                        <Dialog v-model:open="showKategoriModal">
                            <DialogTrigger as-child>
                                <Button type="button" variant="outline" size="sm">+ Kategori Baru</Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Tambah Kategori Baru</DialogTitle>
                                </DialogHeader>
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <Label for="kategori-nama">Nama Kategori</Label>
                                        <Input
                                            id="kategori-nama"
                                            v-model="namaKategoriBaru"
                                            required
                                            placeholder="Contoh: Fiksi, Sejarah, Sains..."
                                        />
                                        <InputError v-if="errorKategori" :message="errorKategori" />
                                    </div>
                                    <Button
                                        type="button"
                                        @click="handleTambahKategoriCepat"
                                    >
                                        Simpan
                                    </Button>
                                </div>
                            </DialogContent>
                        </Dialog>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p v-if="kategoriOptions.length === 0" class="text-sm text-muted-foreground mb-3">
                        Belum ada kategori. Klik "Kategori Baru" di atas untuk menambahkan.
                    </p>
                    <div class="flex flex-wrap gap-2" v-else>
                        <button
                            v-for="k in kategoriOptions"
                            :key="k.id"
                            type="button"
                            class="px-3 py-1.5 rounded text-sm border transition-colors"
                            :class="form.kategori_ids.includes(k.id)
                                ? 'bg-primary text-primary-foreground border-primary'
                                : 'border-muted-foreground/20 hover:border-primary/50'"
                            @click="toggleKategori(k.id)"
                        >
                            {{ k.nama }}
                        </button>
                    </div>
                    <InputError :message="form.errors.kategori_ids" />
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>File</CardTitle></CardHeader>
                <CardContent class="space-y-4">
                    <div class="space-y-2">
                        <Label for="sampul">Sampul Buku</Label>
                        <Input
                            id="sampul"
                            type="file"
                            accept="image/*"
                            @input="form.sampul = ($event.target as HTMLInputElement).files?.[0] ?? null"
                        />
                        <InputError :message="form.errors.sampul" />
                    </div>

                    <div class="space-y-2">
                        <Label for="file_pdf">File PDF</Label>
                        <Input
                            id="file_pdf"
                            type="file"
                            accept=".pdf"
                            :required="!isEditing"
                            @input="form.file_pdf = ($event.target as HTMLInputElement).files?.[0] ?? null"
                        />
                        <InputError :message="form.errors.file_pdf" />
                    </div>
                </CardContent>
            </Card>

            <div class="flex gap-2">
                <Button type="submit" :disabled="form.processing">
                    {{ isEditing ? 'Perbarui' : 'Simpan' }}
                </Button>
                <Button type="button" variant="outline" @click="window.history.back()">
                    Batal
                </Button>
            </div>
        </form>
    </div>
</template>
