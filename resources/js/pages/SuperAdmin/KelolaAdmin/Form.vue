<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import type { User } from '@/types';

const props = defineProps<{
    admin?: User;
}>();

const isEditing = !!props.admin;

const form = useForm({
    name: props.admin?.name ?? '',
    email: props.admin?.email ?? '',
    password: '',
});

const handleSubmit = () => {
    if (isEditing) {
        form.put(`/super-admin/kelola-admin/${props.admin!.id}`);
    } else {
        form.post('/super-admin/kelola-admin');
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Admin' : 'Tambah Admin'" />

    <div class="p-6 max-w-lg space-y-6">
        <h1 class="text-2xl font-semibold">
            {{ isEditing ? 'Edit Admin' : 'Tambah Admin' }}
        </h1>

        <Card>
            <CardHeader>
                <CardTitle>{{ isEditing ? 'Edit Data Admin' : 'Data Admin Baru' }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="name">Nama</Label>
                        <Input id="name" v-model="form.name" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" required />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="space-y-2">
                        <Label for="password">
                            Password {{ isEditing ? '(kosongkan jika tidak diubah)' : '' }}
                        </Label>
                        <Input id="password" v-model="form.password" type="password" :required="!isEditing" />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="flex gap-2">
                        <Button type="submit" :disabled="form.processing">
                            {{ isEditing ? 'Perbarui' : 'Simpan' }}
                        </Button>
                        <Button type="button" variant="outline" @click="window.history.back()">
                            Batal
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
