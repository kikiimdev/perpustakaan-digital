<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { reactive } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Daftar akun',
        description: 'Daftar akun baru untuk mengakses sistem.',
    },
});

const state = reactive({
    password: 'stmik',
    password_confirmation: 'stmik',
});
</script>

<template>
    <Head title="Daftar" />

    <Form
        action="/register"
        method="post"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="nama_anggota">Nama</Label>
                <Input
                    id="nama_anggota"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="nama_anggota"
                    placeholder="Nama lengkap"
                />
                <InputError :message="errors.nama_anggota" />
            </div>

            <div class="grid gap-2">
                <Label for="id_anggota">NRP</Label>
                <Input
                    id="id_anggota"
                    type="text"
                    required
                    :tabindex="2"
                    autocomplete="username"
                    name="id_anggota"
                    placeholder="Masukkan NRP"
                />
                <!-- <p class="text-xs text-muted-foreground">
                    Email akan otomatis dibuat sebagai
                    username@perpustakaan.test
                </p> -->
                <InputError :message="errors.id_anggota" />
            </div>

            <div class="hidden gap-2">
                <Label for="password">Password</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    :passwordrules="passwordRules"
                    v-model="state.password"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="hidden gap-2">
                <Label for="password_confirmation">Konfirmasi password</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Ketik ulang password"
                    :passwordrules="passwordRules"
                    v-model="state.password_confirmation"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Daftar
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Sudah punya akun?
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
                :tabindex="6"
                >Log in</TextLink
            >
        </div>
    </Form>
</template>
