<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Penulis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BukuSeeder extends Seeder
{
    private array $judulBuku = [
        'Laskar Pelangi',
        'Bumi Manusia',
        'Negeri 5 Menara',
        'Sang Pemimpi',
        'Perahu Kertas',
        'Rindu',
        'Hujan',
        'Supernova: Ksatria, Puteri, dan Bintang Jatuh',
        'Pulang',
        'Laut Bercerita',
        'Cantik Itu Luka',
        'Gadis Kretek',
        'Saman',
        'Lelaki Harimau',
        'O',
        'Tentang Kamu',
        'Ayat-Ayat Cinta',
        'Cinta di Dalam Gelas',
        'Pudarnya Pesona Cleopatra',
        'Katarsis',
        'Orang-Orang Biasa',
        'Komet',
            'Daun yang Jatuh Tak Pernah Membenci Angin',
        'Bidadari-Bidadari Surga',
        'Sunset Bersama Rosie',
        'Matahari',
        'Bulan',
            'Bintang',
        'Rumah Kaca',
        'Anak Semua Bangsa',
        'Jejak Langkah',
        'Madogiwa no Totto-chan',
        'Sirkus Pohon',
        'Guru Aini',
        'Orang Miskin Dilarang Sekolah',
        'Kitab Omong Kosong',
        'Sebuah Seni untuk Bersikap Bodo Amat',
        'Filosofi Teras',
        'Atomic Habits',
        'Rich Dad Poor Dad',
        'Sapiens',
        'Homo Deus',
        'Thinking, Fast and Slow',
        'Educated',
        'Man\'s Search for Meaning',
        'Dune',
        'The Hobbit',
        'Harry Potter dan Batu Bertuah',
        'Negeri Para Bedebah',
        '24 Jam Bersama Gaspar',
        'Pergi',
    ];

    public function run(): void
    {
        $penulisIds = Penulis::pluck('id')->toArray();
        $kategoriIds = Kategori::pluck('id')->toArray();

        if (empty($penulisIds) || empty($kategoriIds)) {
            $this->command?->warn('Pastikan penulis dan kategori sudah di-seed terlebih dahulu.');

            return;
        }

        $this->generatePlaceholderCovers();

        $covers = collect(Storage::disk('public')->files('sampul'))->toArray();

        $this->command?->info('Membuat file PDF...');
        Storage::disk('public')->makeDirectory('buku_pdf');

        foreach ($this->judulBuku as $index => $judul) {
            $jumlahHalaman = $index % 2 === 0 ? 30 : 3;

            $html = view('pdf.sample-book', [
                'judul' => $judul,
                'halaman' => $jumlahHalaman,
            ])->render();

            $filename = 'buku_pdf/'.md5($judul.$index).'.pdf';
            Storage::disk('public')->put($filename, Pdf::loadHTML($html)->output());

            $buku = Buku::create([
                'penulis_id' => $penulisIds[array_rand($penulisIds)],
                'judul' => $judul,
                'sinopsis' => $this->sinopsisAcak($judul),
                'sampul' => $covers[array_rand($covers)],
                'file_pdf' => $filename,
                'jumlah_halaman' => $jumlahHalaman,
            ]);

            $buku->kategori()->attach(
                fake()->randomElements($kategoriIds, fake()->numberBetween(1, 3))
            );
        }

        $this->command?->info(count($this->judulBuku).' buku berhasil di-seed.');
    }

    private function sinopsisAcak(string $judul): string
    {
        $templates = [
            '"%s" adalah sebuah karya yang menggugah pemikiran pembaca. Mengisahkan perjalanan hidup yang penuh liku, buku ini mengajak kita merenungi makna kehidupan dan persahabatan.',
            'Dalam "%s", penulis membawa pembaca ke dunia yang penuh misteri dan petualangan. Setiap halaman menyimpan kejutan yang tak terduga.',
            'Novel "%s" mengisahkan tentang cinta, pengorbanan, dan harapan. Sebuah cerita yang akan membuat Anda menangis dan tertawa sekaligus.',
            '"%s" menawarkan perspektif baru tentang kehidupan modern. Dengan gaya penceritaan yang khas, buku ini menjadi bacaan wajib bagi siapa saja.',
            'Buku "%s" menggabungkan fakta sejarah dengan fiksi yang memukau. Perjalanan waktu yang akan membawa Anda ke masa lalu yang penuh warna.',
        ];

        return sprintf($templates[array_rand($templates)], $judul);
    }

    private function generatePlaceholderCovers(): void
    {
        Storage::disk('public')->deleteDirectory('sampul');
        Storage::disk('public')->makeDirectory('sampul');

        $colors = [
            [0x25, 0x56, 0x87], // navy
            [0x76, 0x4b, 0x3e], // brown
            [0x5a, 0x70, 0x50], // moss
            [0x8b, 0x45, 0x3c], // brick
            [0x4a, 0x4e, 0x6d], // slate
            [0x3d, 0x5a, 0x3d], // forest
            [0x6b, 0x4e, 0x6b], // plum
            [0x38, 0x5d, 0x5d], // teal
        ];

        foreach ($this->judulBuku as $index => $judul) {
            $filename = 'sampul/'.md5($judul.$index).'.png';
            $path = Storage::disk('public')->path($filename);

            $width = 400;
            $height = 600;
            $img = imagecreatetruecolor($width, $height);

            [$r, $g, $b] = $colors[$index % count($colors)];
            $bg = imagecolorallocate($img, $r, $g, $b);
            imagefilledrectangle($img, 0, 0, $width, $height, $bg);

            $white = imagecolorallocate($img, 255, 255, 255);
            $fontSize = 5;

            $wrapped = wordwrap($judul, 20, "\n", true);
            $lines = explode("\n", $wrapped);
            $y = ($height - count($lines) * 20) / 2;

            foreach ($lines as $line) {
                $textWidth = strlen($line) * imagefontwidth($fontSize);
                $x = ($width - $textWidth) / 2;
                imagestring($img, $fontSize, (int) $x, (int) $y, $line, $white);
                $y += 22;
            }

            imagepng($img, $path);
            imagedestroy($img);
        }
    }
}
