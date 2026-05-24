export type Penulis = {
    id: number;
    nama: string;
    biografi: string | null;
    created_at: string;
    updated_at: string;
};

export type Kategori = {
    id: number;
    nama: string;
    slug: string;
    created_at: string;
    updated_at: string;
};

export type Buku = {
    id: number;
    penulis_id: number;
    judul: string;
    sinopsis: string;
    sampul: string | null;
    file_pdf: string;
    jumlah_halaman: number;
    created_at: string;
    updated_at: string;
    penulis?: Penulis;
    kategori?: Kategori[];
};

export type BukuFavorit = {
    id: number;
    user_id: number;
    buku_id: number;
    created_at: string;
    updated_at: string;
    buku?: Buku;
};

export type MarkahBuku = {
    id: number;
    user_id: number;
    buku_id: number;
    halaman: number;
    catatan: string | null;
    created_at: string;
    updated_at: string;
};

export type RiwayatBaca = {
    id: number;
    user_id: number;
    buku_id: number;
    halaman_dibaca: number;
    tanggal: string;
    created_at: string;
    updated_at: string;
};
