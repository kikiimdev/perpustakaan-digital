export type StatistikPengguna = {
    bacaan_bulan_ini: number;
    total_halaman: number;
    buku_dibaca: number;
};

export type BukuTerbanyakDibaca = {
    buku_id: number;
    judul: string;
    total_halaman: number;
};

export type BukuTerfavorit = {
    buku_id: number;
    judul: string;
    total_favorit: number;
};

export type AktivitasMembaca = {
    total_pembaca: number;
    total_sesi: number;
    total_halaman: number;
    buku_dibaca: number;
    total_pengguna: number;
    total_buku: number;
};
