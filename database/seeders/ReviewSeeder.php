<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Ulasan contoh untuk sejumlah destinasi paling populer di tiap
     * kategori, supaya fitur rating & ulasan terlihat berfungsi saat
     * didemokan (sebelumnya 0 data). Reviewer dibuat sebagai akun
     * traveler terpisah dari akun demo utama karena kolom unique
     * (user_id, destination_id) hanya izinkan satu ulasan per user
     * per destinasi.
     */
    public function run(): void
    {
        $reviewerNames = [
            'ayu' => 'Ayu Lestari',
            'budi' => 'Budi Santoso',
            'citra' => 'Citra Dewi',
            'dimas' => 'Dimas Prakoso',
            'eka' => 'Eka Putri',
            'fajar' => 'Fajar Nugroho',
            'gita' => 'Gita Permata',
            'hendra' => 'Hendra Wijaya',
        ];

        $reviewers = [];
        foreach ($reviewerNames as $key => $name) {
            $reviewers[$key] = User::updateOrCreate(
                ['email' => $key.'.reviewer@jogjatrip.test'],
                [
                    'name' => $name,
                    'password' => 'password',
                    'role' => 'traveler',
                    'email_verified_at' => now(),
                ]
            );
        }

        $reviews = [
            'keraton-ngayogyakarta-hadiningrat' => [
                ['ayu', 5, 'Wajib dikunjungi kalau ke Jogja. Bangunannya terawat, pemandu di dalam ramah dan ceritanya detail banget soal sejarah Kesultanan.'],
                ['budi', 4, 'Bagus dan edukatif, cuma agak panas siang-siang. Datang pagi lebih nyaman.'],
                ['citra', 5, 'Suasananya kental banget budaya Jawa. Koleksi pusakanya lengkap, cocok buat yang suka sejarah.'],
                ['dimas', 4, 'Tiket masuk murah untuk pengalaman sebagus ini. Kadang ada pertunjukan gamelan juga.'],
            ],
            'taman-sari' => [
                ['eka', 5, 'Spot foto paling ikonik di Jogja. Arsitekturnya unik, perpaduan Jawa dan Eropa kelihatan jelas.'],
                ['fajar', 4, 'Rame banget kalau weekend, disarankan datang pas buka supaya belum terlalu ramai.'],
                ['gita', 5, 'Suka banget sama kolam pemandiannya, history-nya juga menarik untuk digali.'],
            ],
            'museum-benteng-vredeburg' => [
                ['hendra', 4, 'Dioramanya informatif, cocok buat belajar sejarah perjuangan kemerdekaan sambil santai.'],
                ['ayu', 5, 'Ber-AC dan terawat, enak buat istirahat sambil belajar sejarah di tengah panasnya Jogja.'],
                ['citra', 4, 'Lokasinya strategis banget deket Malioboro, gampang dijangkau jalan kaki.'],
            ],
            'kotagede' => [
                ['dimas', 5, 'Suasana kampung lamanya masih kental, banyak rumah joglo tua yang instagramable.'],
                ['eka', 4, 'Cocok buat jalan santai sore hari, banyak gang-gang kecil yang bersejarah untuk dijelajahi.'],
            ],
            'jalan-malioboro' => [
                ['fajar', 5, 'Ikon Jogja banget, dari pagi sampai malam selalu rame dan hidup. Banyak jajanan kaki lima juga.'],
                ['gita', 4, 'Enak buat jalan-jalan sore sambil belanja oleh-oleh, tapi hati-hati jaga barang bawaan karena ramai.'],
                ['hendra', 5, 'Live music jalanan di sini selalu bikin betah, suasananya beda dari kota lain.'],
                ['ayu', 3, 'Rame dan agak panas siang hari, tapi memang wajib dikunjungi sekali kalau ke Jogja.'],
            ],
            'titik-nol-kilometer' => [
                ['budi', 5, 'Spot foto favorit malam hari, bangunan kolonial di sekitarnya cantik banget kalau lampu udah nyala.'],
                ['citra', 4, 'Selalu ramai komunitas dan seniman jalanan, seru buat nongkrong sambil orang-watching.'],
            ],
            'alun-alun-kidul' => [
                ['dimas', 5, 'Masangin-nya seru dicoba! Sewa sepeda hias juga worth it buat malam mingguan sama keluarga.'],
                ['eka', 4, 'Ramai banget tiap malam, cocok buat healing santai sambil nyobain jajanan sekitar.'],
                ['fajar', 5, 'Salah satu memori terbaik di Jogja, anak-anak juga suka banget sama sepeda lampunya.'],
            ],
            'tugu-yogyakarta' => [
                ['gita', 4, 'Landmark wajib difoto kalau ke Jogja, apalagi malam hari waktu lampu-lampunya nyala.'],
                ['hendra', 5, 'Simpel tapi ikonik, selalu jadi titik kumpul enak sebelum lanjut jalan-jalan ke Malioboro.'],
            ],
            'gudeg-yu-djum-wijilan' => [
                ['ayu', 5, 'Gudeg paling otentik yang pernah saya coba, rasa manis gurihnya pas dan nggak eneg.'],
                ['budi', 4, 'Porsinya pas, harga juga masih terjangkau untuk kualitas rasanya.'],
                ['citra', 5, 'Legendaris memang, dari dulu rasanya konsisten enak. Wajib coba kalau ke Jogja.'],
            ],
            'the-house-of-raminten' => [
                ['dimas', 4, 'Suasananya unik banget, cocok buat yang suka nuansa Jawa jadul. Menunya juga variatif.'],
                ['eka', 5, 'Buka 24 jam jadi penyelamat kalau lapar tengah malam. Pelayanannya juga ramah-ramah.'],
                ['fajar', 3, 'Makanan enak tapi agak lama nunggu pas rame, mungkin karena selalu ramai pengunjung.'],
            ],
            'angkringan-lik-man' => [
                ['gita', 5, 'Kopi joss-nya wajib dicoba, sensasi arang panasnya unik dan nggak ada di tempat lain.'],
                ['hendra', 4, 'Harga bersahabat banget buat kantong mahasiswa, suasana nongkrongnya juga asik.'],
                ['ayu', 5, 'Ikon kuliner malam Jogja, sate usus sama kopi jossnya juara.'],
            ],
            'soto-kadipiro' => [
                ['budi', 4, 'Kuahnya bening tapi rasanya gurih, cocok buat sarapan sebelum jalan-jalan keliling kota.'],
                ['citra', 5, 'Soto legendaris, dari dulu sampai sekarang rasanya nggak berubah, tetap enak.'],
            ],
            'hotel-tentrem-yogyakarta' => [
                ['dimas', 5, 'Pelayanan bintang lima beneran, kamarnya luas dan bersih, sarapannya juga enak-enak.'],
                ['eka', 5, 'Lokasi strategis dan fasilitas lengkap, worth it untuk staycation mewah di Jogja.'],
                ['fajar', 4, 'Agak pricey tapi sepadan dengan kualitas kamar dan pelayanannya.'],
            ],
            'the-phoenix-hotel-yogyakarta' => [
                ['gita', 5, 'Suka banget sama nuansa heritage-nya, berasa nginep di bangunan bersejarah tapi tetap nyaman.'],
                ['hendra', 4, 'Lokasinya dekat Tugu Jogja, gampang akses ke mana-mana. Kolam renangnya juga bagus.'],
            ],
            'greenhost-boutique-hotel' => [
                ['ayu', 4, 'Konsep eco-friendly-nya kerasa banget, taman atapnya juga jadi spot foto favorit.'],
                ['budi', 5, 'Dekat banget sama kawasan kuliner Prawirotaman, jadi gampang cari makan malam.'],
            ],
            'pasar-beringharjo' => [
                ['citra', 5, 'Surganya batik dan oleh-oleh, harga bisa ditawar dan pilihannya banyak banget.'],
                ['dimas', 4, 'Agak sesak kalau weekend tapi worth it buat cari batik dengan harga miring.'],
                ['eka', 4, 'Pedagangnya ramah, cocok buat belajar tawar-menawar ala pasar tradisional.'],
            ],
            'bakpia-pathok-25' => [
                ['fajar', 5, 'Bakpia paling legendaris, rasa original kacang hijaunya tetap juara dari dulu.'],
                ['gita', 4, 'Selalu jadi oleh-oleh wajib tiap pulang dari Jogja, kemasannya juga awet buat dibawa jauh.'],
                ['hendra', 5, 'Enak dan harganya masuk akal, cocok buat oleh-oleh ke banyak orang.'],
            ],
            'dagadu-jogja' => [
                ['ayu', 4, 'Desain kaosnya kreatif dan kekinian, khas anak muda Jogja banget.'],
                ['budi', 5, 'Oleh-oleh favorit buat teman-teman kantor, desainnya selalu update dan lucu-lucu.'],
            ],
            'kampung-wisata-tamansari' => [
                ['citra', 5, 'Suka banget jalan-jalan di lorong-lorongnya, mural warna-warninya bikin betah foto-foto.'],
                ['dimas', 4, 'Pengalaman beda dari wisata Taman Sari biasa, lebih terasa kehidupan kampung kotanya.'],
            ],
            'kampung-wisata-purbayan' => [
                ['eka', 5, 'Bisa lihat langsung proses bikin kerajinan perak, seru dan edukatif banget buat anak-anak.'],
                ['fajar', 4, 'Pengrajinnya ramah, mau jelasin proses pembuatan sampai selesai. Recommended!'],
            ],
        ];

        foreach ($reviews as $slug => $entries) {
            $destination = Destination::where('slug', $slug)->first();

            if (! $destination) {
                continue;
            }

            foreach ($entries as [$reviewerKey, $rating, $comment]) {
                $destination->reviews()->updateOrCreate(
                    ['user_id' => $reviewers[$reviewerKey]->id],
                    [
                        'rating' => $rating,
                        'comment' => $comment,
                        'is_visible' => true,
                    ]
                );
            }
        }
    }
}
