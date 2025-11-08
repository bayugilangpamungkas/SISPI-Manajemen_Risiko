<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Uraian;

class UraianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $uraian = [
            [
                'id_management_topic' => 1,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan/peraturan tentang analisis jabatan.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan/panduan rekrutmen.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 2,
                'uraian' => 'Kebijakan analisis jabatan mencakup tahapan persiapan,
                 pengumpulan data jabatan, pengolahan data jabatan, verifikasi jabatan
                  dan penetapan hasil analisis jabatan.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 2,
                'uraian' => 'Kebijakan/panduan rekrutmen mencakup kualifikasi/kriteria
                 SDM yang dibutuhkan berdasarkan analisis jabatan dan uraian jabatan.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 2,
                'uraian' => 'SPI BLU telah melakukan perencanaan SDM.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 3,
                'uraian' => 'SPI BLU mengidentifikasi kebutuhan dan merekrut SDM kompeten
                 untuk melaksanakan  rencana  pengawasan intern berdasarkan analisis jabatan
                  dan uraian jabatan.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 3,
                'uraian' => 'Proses rekrutmen dilakukan dengan benar,kredibel terbuka, adil
                 dan transparan untuk memperoleh  SDM  kompeten yang sesuai dengan kualifikasi
                 /kriteria.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 3,
                'uraian' => 'Penempatan dan pola mutasi SDM SPI BLU telah sesuai dengan kebutuhan.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 4,
                'uraian' => 'Analisis jabatan dan uraian jabatan  telah dilaksanakan dan dievaluasi
                 secara berkelanjutan sesuai dengan kebutuhan  SPI BLU.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 4,
                'uraian' => 'Identifikasi dan rekrutmen SDM telah dilaksanakan dan dievaluasi secara
                 berkelanjutan sesuai dengan kebutuhan SDM SPI BLU.'
            ],
            [
                'id_management_topic' => 1,
                'level' => 5,
                'uraian' => 'Perencanaan SDM telah adaptif terhadap perubahan lingkungan strategis.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan pengembangan SDM.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 2,
                'uraian' => 'Memuat proses/mekanisme/kriteria untuk   mengembangkan kompetensi dalam
                 melaksanakan aktivitas pengawasan meliputi kompetensi teknis dan kompetensi manajerial.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 2,
                'uraian' => 'Mengatur  jam pelatihan minimal (jam/hari/jenis) yang harus terpenuhi dalam
                 rangka pengembangan profesi individu auditor.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 2,
                'uraian' => 'Mengatur proses/mekanisme/kriteria pemberian penghargaan individu dan tim.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 2,
                'uraian' => 'Mendorong SDM SPI BLU untuk meningkatkan kompetensinya melalui organisasi
                 profesi/asosiasi/lembaga.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 2,
                'uraian' => 'SPI BLU telah melakukan kegiatan pengembangan SDM.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 3,
                'uraian' => 'Memiliki peta kompetensi berdasarkan kerangka kompetensi dan memuat analisis gap.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 3,
                'uraian' => 'Menghasilkan kompetensi sesuai kebutuhan pengawasan, baik kompetensi dasar maupun
                 sertifikasi profesional penunjang pengawasan (CIA, CGAP, CFE, CFrA,CGCAE dan sebagainya).'
            ],
            [
                'id_management_topic' => 2,
                'level' => 3,
                'uraian' => 'Membentuk setiap personal  untuk dapat berperan secara efektif dan bekerjasama
                 dalam tim (team building).'
            ],
            [
                'id_management_topic' => 2,
                'level' => 3,
                'uraian' => 'Pengembangan SDM telah memenuhi jam pelatihan minimal selama setahun'
            ],
            [
                'id_management_topic' => 2,
                'level' => 3,
                'uraian' => 'Pemberian penghargaan terhadap individu dan tim yang mencapai
                 kriteria berprestasi yang telah didukung dengan SK tim penilai dan Sertifikat penghargaan
                  Tim dan Individu.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 3,
                'uraian' => 'SDM SPI BLU berpartisipasi aktif dalam organisasi profesi/asosiasi misalnya
                 menjadi pengurus, mengikuti seminar/rapat, dsb.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 4,
                'uraian' => 'Pengembangan SDM SPI BLU telah dilaksanakan dan dievaluasi secara
                 berkelanjutan sesuai dengan kebutuhan organisasi SPI BLU.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 4,
                'uraian' => 'SPI BLU telah menyusun proyeksi kebutuhan kompetensi dan
                 keahlian SDM jangka panjang yang selaras dengan rencana strategis organisasi
                  K/L/D termasuk analisis gap dan strategi pemenuhannya.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 4,
                'uraian' => 'Memanfaatkan pengetahuan yang diperoleh SDM SPI BLU atas
                 partisipasi pada organisasi profesi untuk meningkatkan aktivitas pengawasan intern.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 4,
                'uraian' => 'Pengembangan SDM SPI BLU menjadi role model bagi pengembangan SDM di lingkungan K/L/D.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 5,
                'uraian' => 'Pengembangan SDM telah adaptif terhadap perubahan lingkungan strategis.'
            ],
            [
                'id_management_topic' => 2,
                'level' => 5,
                'uraian' => 'Pengembangan SDM SPI BLU menjadi role model/benchmark bagi pengembangan SDM SPI BLU lainnya.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan/pedoman penyusunan perencanaan pengawasan tahunan (PKPT).'
            ],
            [
                'id_management_topic' => 3,
                'level' => 2,
                'uraian' => 'Kebijakan/pedoman penyusunan PKPT telah mengakomodasi prioritas manajemen, berbasis
                 risiko dan selaras dengan kebijakan/peraturan manajemen risiko organisasi.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 2,
                'uraian' => 'SPI BLU telah memiliki PKPT.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 3,
                'uraian' => 'Mengidentifikasi keselarasan visi, misi, tujuan, sasaran organisasi K/L/D
                 serta indikator kinerja capaian sasaran dan pemahaman proses bisnisnya.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 3,
                'uraian' => 'Mengidentifikasi semua area pengawasan yang dapat dijadikan  sasaran 
                pengawasan atau audit universe  (antara lain: urusan, unit kerja, program, kegiatan, 
                fungsi yang dapat diawasi).'
            ],
            [
                'id_management_topic' => 3,
                'level' => 3,
                'uraian' => 'Mengidentifikasi dan membuat prioritas area pengawasan berdasarkan
                 tingkat kematangan MR dan risiko tertinggi berdasarkan hasil evaluasi.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 3,
                'uraian' => 'Mempertimbangkan masukan dari manajemen K/L/D dan stakeholder lainnya.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 3,
                'uraian' => 'Mengidentifikasi dan menganalisis ketersediaan sumber daya (SDM, waktu,
                 dana) termasuk penjelasan bila sumber daya tidak tercukupi.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 3,
                'uraian' => 'Menetapkan jenis-jenis pengawasan, sasaran, ruang lingkup, jadwal
                 pelaksanaan, anggaran, SDM dan informasi lainnya.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 3,
                'uraian' => 'Mendapatkan persetujuan pimpinan organisasi K/L/D.'
            ],
            [
                'id_management_topic' => 3,
                'level' => 4,
                'uraian' => 'Perencanaan pengawasan dievaluasi secara berkelanjutan '
            ],
            [
                'id_management_topic' => 3,
                'level' => 4,
                'uraian' => 'Perencanaan pengawasan menggunakan profil risiko
                 organisasi K/L/D secara keseluruhan (Enterprise Risk Management (ERM)).'
            ],
            [
                'id_management_topic' => 3,
                'level' => 5,
                'uraian' => 'Perencanaan pengawasan bersifat foresight'
            ],
            [
                'id_management_topic' => 4,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan tentang program
                 penjaminan dan peningkatan kualitas (Quality Assurance and Improvement Program/QAIP).'
            ],
            [
                'id_management_topic' => 4,
                'level' => 2,
                'uraian' => 'Penilaian intern (pemantauan berkelanjutan atas kinerja pengawasan intern/reviu
                 berjenjang dan penilaian berkala yang dilakukan secara mandiri atau oleh pihak lain dalam organisasi).'
            ],
            [
                'id_management_topic' => 4,
                'level' => 2,
                'uraian' => 'Penilaian ekstern/telaah sejawat.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 2,
                'uraian' => 'Melaksanakan dan mendokumentasikan program penjaminan dan peningkatan kualitas (QAIP).'
            ],
            [
                'id_management_topic' => 4,
                'level' => 2,
                'uraian' => 'Memonitor dan melaporkan kinerja dan efektivitas kegiatan SPI BLU.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Pemantauan berkelanjutan atas kinerja pengawasan intern/reviu berjenjang yang dilakukan
                 untuk mengevaluasi kesesuaian pelaksanaan kegiatan pengawasan intern sehari-hari dengan kode etik
                  dan standar, meliputi: perencanaan penugasan, pelaksanaan penugasan, komunikasi hasil penugasan,
                   pemantauan tindak lanjut.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Penilaian berkala yang dilakukan secara mandiri atau oleh pihak lain dalam organisasi
                 untuk mengevaluasi kesesuaian pelaksanaan kegiatan pengawasan intern dalam suatu periode dengan
                  definisi pengawasan intern, kode etik, dan standar audit.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Penilaian ekstern dilaksanakan secara berkala sesuai dengan standar.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Ruang lingkup dan frekuensi, baik atas penilaian intern dan ekstern.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Kualifikasi dan independensi penilai atau tim penilai, termasuk potensi benturan kepentingan.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Kesimpulan penilai atau tim penilai.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Rencana tindak perbaikan.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Telah mengembangkan sistem dan prosedur untuk menindaklanjuti hasil QAIP berupa pelaksanaan
                 rekomendasi perbaikan yang dibuat dalam rangka meningkatkan efektivitas kegiatan pengawasan intern dan
                  kesesuaian dengan standar.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 3,
                'uraian' => 'Meningkatnya  kepercayaan stakeholders dengan adanya dokumentasi atas komitmen SPI BLU
                 terhadap kualitas penyelenggaraan pengawasan intern.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 4,
                'uraian' => 'Kebijakan dan pelaksanaan program penjaminan dan peningkatan kualitas (QAIP) dievaluasi
                 secara berkelanjutan.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 4,
                'uraian' => 'Berbagi pengetahuan dengan pimpinan K/L/D perihal praktik kerja unggulan dan peningkatan
                 kinerja untuk mendapatkan dukungan peningkatan kegiatan pengawasan dan organisasi secara berkelanjutan.'
            ],
            [
                'id_management_topic' => 4,
                'level' => 5,
                'uraian' => 'Berkontribusi untuk asosiasi profesi yang relevan guna mendapatkan wawasan dan pembelajaran
                 berkelanjutan, serta penerapan praktik-praktik terbaik pengawasan intern secara global.'
            ],
            [
                'id_management_topic' => 5,
                'level' => 1,
                'uraian' => 'Terdapat Renja SPI BLU yang telah ditetapkan.'
            ],
            [
                'id_management_topic' => 5,
                'level' => 1,
                'uraian' => 'Terdapat RKA SPI BLU yang telah ditetapkan.'
            ],
            [
                'id_management_topic' => 5,
                'level' => 2,
                'uraian' => 'Mengidentifikasi sasaran dan hasil yang ingin
                 dicapai, serta ukuran keberhasilan pencapaian (indikator kinerja) yang relevan.'
            ],
            [
                'id_management_topic' => 5,
                'level' => 2,
                'uraian' => 'Mengidentifikasi aktivitas yang relevan dalam pencapaian sasaran
                 dan hasil yang akan dicapai (program kegiatan, sub kegiatan dan lainnya).'
            ],
            [
                'id_management_topic' => 5,
                'level' => 2,
                'uraian' => 'Menyajikan alokasi anggaran yang dibutuhkan.'
            ],
            [
                'id_management_topic' => 5,
                'level' => 2,
                'uraian' => 'RKA SPI BLU selaras dengan Renja SPI BLU.'
            ],
            [
                'id_management_topic' => 5,
                'level' => 3,
                'uraian' => 'Renja dan RKA SPI BLU menjadi acuan dalam penyusunan Perjanjian kinerja SPI BLU. '
            ],
            [
                'id_management_topic' => 5,
                'level' => 3,
                'uraian' => 'Renja, RKA, dan Perjanjian Kinerja SPI BLU menjadi acuan dalam penyusunan PKPT'
            ],
            [
                'id_management_topic' => 5,
                'level' => 3,
                'uraian' => 'Renja dan RKA SPI BLU menjadi dasar untuk mengendalikan kegiatan pengawasan
                 dan panduan bagi pimpinan SPI BLU untuk mempertanggungjawabkan penggunaan sumber daya dalam
                  mencapai tujuan pengawasan.'
            ],
            [
                'id_management_topic' => 5,
                'level' => 4,
                'uraian' => 'Renja dan RKA SPI BLU direviu secara berkelanjutan untuk memastikan bahwa
                 kegiatan dan anggaran yang disusun tetap realistis dan akurat dengan mempertimbangkan 
                 perubahan lingkungan strategis.'
            ],
            [
                'id_management_topic' => 5,
                'level' => 5,
                'uraian' => 'Renja dan RKA SPI BLU telah bersifat adaptif.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan pelaksanaan anggaran SPI BLU.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 2,
                'uraian' => 'Kebijakan pelaksanaan anggaran SPI BLU telah
                 mengatur sistem pelaksanaan anggaran berupa, prosedur, otorisasi,
                  klasifikasi, dan pencatatan pelaksanaan anggaran.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 2,
                'uraian' => 'SPI BLU menggunakan sistem pelaksanaan anggaran.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 3,
                'uraian' => 'Selaras dengan sistem manajemen keuangan dan operasional
                 K/L/D serta pelaporannya.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 3,
                'uraian' => 'Mengadministrasikan keseluruhan biaya yang timbul dalam
                 proses pemberian layanan pengawasan intern.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 3,
                'uraian' => 'Menghasilkan rincian realisasi anggaran kegiatan secara akurat.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 3,
                'uraian' => 'Dapat memantau realisasi biaya dengan anggaran untuk setiap jenis kegiatan.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 3,
                'uraian' => 'Dipantau secara berkala untuk memastikan bahwa struktur biaya masih
                 relevan, efisien dan ekonomis.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 3,
                'uraian' => 'Menghasilkan informasi penggunaan sumber daya, pengeluaran biaya yang
                 melebihi anggaran (overruns), dan penghematan biaya (cost saving).'
            ],
            [
                'id_management_topic' => 6,
                'level' => 3,
                'uraian' => 'Dimanfaatkan untuk pengendalian biaya program/kegiatan pengawasan
                 sebagai salah satu dasar pengambilan keputusan.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 4,
                'uraian' => 'Sistem pelaksanaan anggaran SPI BLU telah dievaluasi secara berkelanjutan.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 4,
                'uraian' => 'Hasil evaluasi pelaksanaan anggaran SPI BLU digunakan sebagai dasar
                 perbaikan perencanaan dan pelaksanaan periode berikutnya.'
            ],
            [
                'id_management_topic' => 6,
                'level' => 5,
                'uraian' => 'SPI BLU mampu menciptakan inovasi sistem pelaksanaan anggaran sehingga
                 informasi yang dibutuhkan dapat disajikan secara real-time untuk mendukung proses
                  pengambilan keputusan.'
            ],
            [
                'id_management_topic' => 7,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan penyusunan pelaporan kepada manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 7,
                'level' => 2,
                'uraian' => 'Kebijakan penyusunan pelaporan kepada manajemen K/L/D 
                telah mengatur tentang pelaporan kinerja dan anggaran SPI BLU.'
            ],
            [
                'id_management_topic' => 7,
                'level' => 2,
                'uraian' => 'SPI BLU telah menyusun laporan akuntabilitas kepada manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 7,
                'level' => 3,
                'uraian' => 'Mengidentifikasi pengelolaan keuangan dan capaian kinerja 
                (capaian kinerja, hambatan dan atau faktor keberhasilan pencapaian kinerja, 
                aktivitas dalam pencapaian kinerja, dan penggunaan sumber daya).'
            ],
            [
                'id_management_topic' => 7,
                'level' => 3,
                'uraian' => 'Menyediakan informasi yang relevan serta dilaporkan secara 
                tepat waktu dan berkala kepada manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 7,
                'level' => 4,
                'uraian' => 'Laporan akuntabilitas kepada manajemen K/L/D dievaluasi
                 penggunaannya secara berkelanjutan untuk memastikan informasi telah 
                 relevan dan tepat guna, serta dilakukan perbaikan apabila diperlukan.'
            ],
            [
                'id_management_topic' => 7,
                'level' => 5,
                'uraian' => 'SPI BLU mampu menciptakan inovasi pelaporan kepada manajemen
                 K/L/D sehingga informasi yang dibutuhkan dapat disajikan secara real-time
                  untuk mendukung proses pengambilan keputusan.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan pengukuran indikator kinerja.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 1,
                'uraian' => 'Perjanjian kinerja Pimpinan SPI BLU telah ditetapkan'
            ],
            [
                'id_management_topic' => 8,
                'level' => 2,
                'uraian' => 'Perjanjian kinerja Pimpinan SPI BLU telah berorientasi hasil.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 2,
                'uraian' => 'Perjanjian kinerja Pejabat Pengawasan di lingkungan SPI BLU
                 (Inspektur Wilayah, Inspektur Pembantu,dsb) telah ditetapkan.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 2,
                'uraian' => 'Kebijakan pengukuran kinerja telah mengatur tentang mekanisme
                 pengumpulan data, metode pengukuran, dan periode serta ruang lingkup monitoring-evaluasi.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 2,
                'uraian' => 'SPI BLU telah melaksanakan pengukuran kinerja.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 3,
                'uraian' => 'Perjanjian kinerja Pejabat Pengawasan di lingkungan
                 SPI BLU (Inspektur Wilayah, Inspektur Pembantu,dsb) telah selaras
                  dengan Perjanjian Kinerja Pimpinan SPI BLU.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 3,
                'uraian' => 'Perjanjian kinerja/SKP telah ditetapkan untuk seluruh
                 individu di lingkungan SPI BLU.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 3,
                'uraian' => 'Digunakan untuk mengukur kinerja pada level organisasi SPI BLU.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 3,
                'uraian' => 'Digunakan untuk mengukur kinerja aktivitas pengawasan (pelaksanaan PKPT).'
            ],
            [
                'id_management_topic' => 8,
                'level' => 3,
                'uraian' => 'Dipantau secara berkala.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 4,
                'uraian' => 'Perjanjian kinerja/SKP untuk seluruh individu di lingkungan SPI BLU telah
                 selaras dan mendukung kinerja Pimpinan SPI BLU.	'
            ],
            [
                'id_management_topic' => 8,
                'level' => 4,
                'uraian' => 'Sistem pengukuran kinerja telah dievaluasi secara berkelanjutan dalam mendukung
                 pencapaian tujuan serta mewujudkan akuntabilitas SPI BLU.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 4,
                'uraian' => 'Implementasi sistem pengukuran kinerja telah menghasilkan perbaikan pencapaian
                 kinerja.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 4,
                'uraian' => 'Impementasi sistem pengukuran kinerja telah dilaksanakan sampai dengan level individu.'
            ],
            [
                'id_management_topic' => 8,
                'level' => 5,
                'uraian' => 'Sistem pengukuran kinerja telah menggambarkan capaian kinerja peran dan layanan
                 SPI BLU yang real- time.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 1,
                'uraian' => 'Terdapat Struktur Organisasi Tata Kerja (SOTK) SPI BLU atau peraturan lain yang sejenis.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan koordinasi dan/atau komunikasi internal.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 2,
                'uraian' => 'Struktur organisasi SPI BLU telah sesuai dengan kebutuhan
                 untuk melaksanakan aktivitas pengawasan dan ditetapkan secara formal,
                  yang memuat kedudukan, tugas dan fungsi, serta tata kerja SPI BLU.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 2,
                'uraian' => 'Kebijakan koordinasi atau komunikasi internal telah 
                mengatur hubungan intern yang dinamis di lingkungan SPI BLU.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 2,
                'uraian' => 'SPI BLU telah mengelola dan mengembangkan hubungan komunikasi intern.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 3,
                'uraian' => 'Dalam mengelola komunikasi intern SPI BLU telah mengidentifikasi peran
                 dan tanggung jawab untuk mengatur hubungan pelaporan antar individu dalam setiap kegiatan pengawasan.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 3,
                'uraian' => 'Pembekalan kepada tim audit oleh pimpinan SPI BLU.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 3,
                'uraian' => 'Forum-forum komunikasi internal maupun forum ekspos hasil pengawasan termasuk
                 pembahasan notisi audit.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 3,
                'uraian' => 'SPI BLU telah mendiskusikan rencana organisasi K/L/D, informasi penting, 
                dan isu-isu terkini dengan seluruh staf di lingkungan SPI BLU.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 3,
                'uraian' => 'Pola koordinasi dan sistem komunikasi SPI BLU memberikan kesempatan bagi
                 setiap individu untuk berpendapat dan menyampaikan saran terkait aktivitas pengawasan.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 3,
                'uraian' => 'Pengelolaan proses bisnis pengawasan intern SPI BLU dan hubungan
                 komunikasi internal SPI BLU meningkatkan efektivitas dan efisiensi aktivitas pengawasan.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 4,
                'uraian' => 'Pola koordinasi dan sistem komunikasi internal SPI BLU telah dievaluasi 
                dan dilaksanakan secara berkelanjutan dalam mewujudkan budaya komunikasi yang konstruktif 
                terhadap aktivitas pengawasan intern.'
            ],
            [
                'id_management_topic' => 9,
                'level' => 5,
                'uraian' => 'Koordinasi dan sistem komunikasi internal SPI BLU yang menjadi best
                 practice dalam budaya dan hubungan organisasi.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan tentang komunikasi antara pimpinan SPI BLU dengan pimpinan K/L/D.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan tentang SPI BLU dilibatkan dalam forum komunikasi.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 2,
                'uraian' => 'Kebijakan komunikasi telah mendorong pimpinan SPI BLU untuk
                 berpartisipasi dalam forum bersama K/L/D.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 2,
                'uraian' => 'Kebijakan forum komunikasi mengatur tentang jadwal pertemuan 
                berkala, dan substansi pembahasan.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 2,
                'uraian' => 'SPI BLU telah berpartisipasi dalam forum K/L/D.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 3,
                'uraian' => 'SPI BLU berpartisipasi dalam forum bersama K/L/D
                 untuk memahami permasalahan secara umum, kondisi yang dihadapi,
                  dan menyampaikan pandangannya sebagai upaya pemecahan masalah.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 3,
                'uraian' => 'SPI BLU berpartisipasi dalam forum organisasi strategis seperti
                 Satgas Covid-19.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 4,
                'uraian' => 'Partisipasi SPI BLU dalam forum K/L/D telah dilaksanakan dan dievaluasi secara berkelanjutan.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 4,
                'uraian' => 'SPI BLU telah membagikan pengetahuan dan pengalaman tentang praktik terbaik dalam
                 pengawasan intern dan proses bisnis kepada seluruh Satker/OPD.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 5,
                'uraian' => 'SPI BLU berperan strategis dalam forum komunikasi K/L/D.'
            ],
            [
                'id_management_topic' => 10,
                'level' => 5,
                'uraian' => 'SPI BLU dengan pengetahuan dan pengalamannya dipandang sebagai partner yang 
                kredibel dan terpercaya.'
            ],
            [
                'id_management_topic' => 11,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan berbagi informasi, berkomunikasi, dan berkoordinasi 
                dengan pihak lain yang memberikan saran dan penjaminan.'
            ],
            [
                'id_management_topic' => 11,
                'level' => 2,
                'uraian' => 'Kebijakan berbagi informasi, berkomunikasi, dan berkoordinasi dengan pihak
                 lain yang memberikan saran dan penjaminan telah mencakup ruang lingkup, tujuan, dan hasil
                  yang akan diberikan.'
            ],
            [
                'id_management_topic' => 11,
                'level' => 2,
                'uraian' => 'SPI BLU melakukan kegiatan berbagi informasi, berkomunikasi, dan berkoordinasi 
                dengan pihak lain yang memberikan saran dan penjaminan.'
            ],
            [
                'id_management_topic' => 11,
                'level' => 3,
                'uraian' => 'SPI BLU telah mengidentifikasi area pengawasan (perencanaan, informasi, dan 
                hasil) yang akan dibagikan kepada pihak lain.'
            ],
            [
                'id_management_topic' => 11,
                'level' => 3,
                'uraian' => 'SPI BLU melakukan kegiatan berbagi informasi, berkomunikasi, dan
                 berkoordinasi dengan pihak lain dalam rangka meminimalkan duplikasi pengawasan dan 
                 memaksimalkan cakupan pengawasan.'
            ],
            [
                'id_management_topic' => 11,
                'level' => 4,
                'uraian' => 'SPI BLU secara berkelanjutan mengidentifikasi area pengawasan 
                dan berbagi informasi, berkomunikasi, dan berkoordinasi dengan pihak lain terkait 
                area pengawasan tersebut serta mengevaluasinya terus menerus.'
            ],
            [
                'id_management_topic' => 11,
                'level' => 5,
                'uraian' => 'Kegiatan berbagi informasi, berkomunikasi, dan berkoordinasi dengan pihak
                 lain yang memberikan saran dan penjaminan telah efektif dalam memastikan cakupan pengawasan
                  yang lebih utuh dan strategis'
            ],
            [
                'id_management_topic' => 12,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan yang memberikan SPI BLU kewenangan untuk mengajukan 
                anggaran/revisi anggaran dalam melaksanakan aktivitas pengawasan intern.'
            ],
            [
                'id_management_topic' => 12,
                'level' => 2,
                'uraian' => 'Terdapat kebijakan pengajuan anggaran/ revisi anggaran dan persetujuannya 
                sesuai dengan peraturan yang berlaku.'
            ],
            [
                'id_management_topic' => 12,
                'level' => 2,
                'uraian' => 'SPI BLU telah mendapatkan kepastian alokasi anggaran.'
            ],
            [
                'id_management_topic' => 12,
                'level' => 3,
                'uraian' => 'Memperhatikan sumber daya yang diperlukan untuk melaksanakan aktivitas
                 pengawasan intern (assurance dan consulting services).'
            ],
            [
                'id_management_topic' => 12,
                'level' => 3,
                'uraian' => 'Ditetapkan melalui mekanisme/proses yang transparan sesuai peraturan yang berlaku.'
            ],
            [
                'id_management_topic' => 12,
                'level' => 4,
                'uraian' => 'Dengan memperhatikan sumber daya yang diperlukan untuk melaksanakan aktivitas 
                pengawasan intern (assurance dan consulting services).'
            ],
            [
                'id_management_topic' => 12,
                'level' => 4,
                'uraian' => 'Ditetapkan melalui mekanisme/proses yang transparan sesuai peraturan yang berlaku'
            ],
            [
                'id_management_topic' => 12,
                'level' => 5,
                'uraian' => 'Pimpinan K/L/D menjamin ketersediaan anggaran pengawasan.'
            ],
            [
                'id_management_topic' => 13,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan terkait akses terhadap informasi organisasi, aset, dan SDM K/L/D.'
            ],
            [
                'id_management_topic' => 13,
                'level' => 2,
                'uraian' => 'Kebijakan telah memuat kewenangan dalam mengakses informasi organisasi, aset, dan 
                SDM K/L/D serta penanganan saat terjadi pembatasan akses atau intervensi oleh Pimpinan K/L/D.'
            ],
            [
                'id_management_topic' => 13,
                'level' => 2,
                'uraian' => 'SPI BLU dapat mengakses informasi organisasi, aset, dan SDM K/L/D dalam setiap penugasan.'
            ],
            [
                'id_management_topic' => 13,
                'level' => 3,
                'uraian' => 'Dapat mengakses informasi organisasi, aset dan SDM K/L/D secara penuh tanpa pembatasan
                 dan intervensi.'
            ],
            [
                'id_management_topic' => 13,
                'level' => 3,
                'uraian' => 'jika terdapat pembatasan akses, SPI BLU menyampaikan kepada Pimpinan K/L/D dan 
                mendiskusikan implikasinya.'
            ],
            [
                'id_management_topic' => 13,
                'level' => 3,
                'uraian' => 'jika terdapat intervensi oleh Pimpinan K/L/D, SPI BLU mendiskusikan implikasinya
                 kepada Pimpinan K/L/D.'
            ],
            [
                'id_management_topic' => 13,
                'level' => 4,
                'uraian' => 'SPI BLU telah melaksanakan aktivitas pengawasan tanpa pembatasan akses dan 
                intervensi serta dievaluasi secara berkelanjutan.'
            ],
            [
                'id_management_topic' => 13,
                'level' => 5,
                'uraian' => 'SPI BLU mendapat akses penuh terhadap informasi organisasi, 
                aset, dan SDM K/L/D secara real time.'
            ],
            [
                'id_management_topic' => 14,
                'level' => 1,
                'uraian' => 'Terdapat kebijakan pelaporan dan komunikasi kepada manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 14,
                'level' => 2,
                'uraian' => 'Kebijakan pelaporan dan komunikasi telah memuat mekanisme atau 
                prosedur pelaporan kepada manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 14,
                'level' => 2,
                'uraian' => 'Pimpinan SPI BLU melaksanakan kegiatan pelaporan dan 
                komunikasi kepada manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 14,
                'level' => 3,
                'uraian' => 'Pimpinan SPI BLU menyampaikan laporan kegiatan SPI 
                BLU sesuai standar kepada manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 14,
                'level' => 4,
                'uraian' => 'Kebijakan pelaporan kegiatan SPI BLU telah dievaluasi secara berkala.'
            ],
            [
                'id_management_topic' => 14,
                'level' => 4,
                'uraian' => 'Kegiatan pelaporan oleh Pimpinan SPI BLU sesuai standar telah 
                dilakukan secara berkelanjutan.'
            ],
            [
                'id_management_topic' => 14,
                'level' => 5,
                'uraian' => 'SPI BLU mampu melaksanakan kegiatan pelaporan secara real-time.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 1,
                'uraian' => 'Terdapat Internal Audit Charter (IAC) atau dokumen lain yang dipersamakan.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 1,
                'uraian' => 'Terdapat Pedoman/Petunjuk Pelaksanaan Audit Ketaatan.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 1,
                'uraian' => 'SPI BLU melaksanakan audit ketaatan.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 2,
                'uraian' => 'IAC telah memuat mandat audit ketaatan.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 2,
                'uraian' => 'Pedoman/Petunjuk pelaksanaan audit 
                ketaatan telah memuat minimal perencanaan audit, pelaksanaan
                 audit dan pengkomunikasian hasil audit.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 2,
                'uraian' => 'SPI BLU melaksanakan audit ketaatan dengan SDM yang memiliki kompetensi.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Perencanaan audit ketaatan telah: dikomunikasikan kepada 
                stakeholder/auditee/manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Perencanaan audit ketaatan telah: mengidentifikasi kriteria-kriteria yang akan digunakan.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Perencanaan audit ketaatan telah: menilai Sistem Pengendalian Intern (SPI) 
                termasuk mengidentifikasi/menilai risiko spesifik audit dan mereviu pengendalian kunci/utama.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Perencanaan audit ketaatan telah: mengidentifikasi sasaran, ruang lingkup 
                dan metodologi audit (termasuk PAO, TAO, dan metodologi pengambilan sampel).'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Perencanaan audit ketaatan telah: mengembangkan Program Kerja Audit.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit ketaatan telah: dilakukan oleh SDM yang 
                memiliki kompetensi terkait audit ketaatan.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit ketaatan telah: mendokumentasikan prosedur 
                dan hasilnya dalam Kertas Kerja Audit (KKA).'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit ketaatan telah: mengevaluasi informasi/bukti audit yang diperoleh.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit ketaatan telah: mendeteksi ada tidaknya indikasi terjadinya 
                penyimpangan dari ketentuan peraturan perundang-undangan, kecurangan dan, ketidakpatutan (abuse).'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit ketaatan telah: melalui supervisi dan reviu berjenjang.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit ketaatan telah: membuat simpulan dan menyusun rekomendasi.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Hasil audit ketaatan telah: dikomunikasikan kepada manajemen K/L/D 
                melalui laporan hasil audit ketaatan.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Hasil audit ketaatan telah: didukung prosedur untuk memonitor tindak 
                lanjut rekomendasi hasil audit serta bukti pelaksanaan tindak lanjut.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 3,
                'uraian' => 'Kualitas Pengawasan'
            ],
            [
                'id_management_topic' => 15,
                'level' => 4,
                'uraian' => 'Audit ketaatan telah dilaksanakan secara berkelanjutan (terinternalisasi).'
            ],
            [
                'id_management_topic' => 15,
                'level' => 4,
                'uraian' => 'Pedoman dan pelaksanaan audit ketaatan telah dievaluasi dan disesuaikan 
                secara terus menerus sesuai kebutuhan dan perubahan lingkungan strategis.'
            ],
            [
                'id_management_topic' => 15,
                'level' => 4,
                'uraian' => 'Audit ketaatan telah menghasilkan kualitas pengawasan yang konsisten 
                dan berkelanjutan dalam rangka perbaikan GRC (contoh: tidak terdapat permasalahan berulang).'
            ],
            [
                'id_management_topic' => 15,
                'level' => 5,
                'uraian' => 'SPI BLU melakukan inovasi dalam praktik pengawasan ketaatan yang adaptif 
                terhadap perubahan lingkungan strategis. Hasil pengawasan ketaatan memberikan foresight 
                dan keyakinan bagi manajemen K/L/D dalam memastikan tidak terjadinya permasalahan ketaatan, 
                tindak penyimpangan dan/atau korupsi.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 1,
                'uraian' => 'Terdapat Internal Audit Charter (IAC) atau dokumen lain yang dipersamakan.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 1,
                'uraian' => '"Terdapat Pedoman/Petunjuk Pelaksanaan Audit Kinerja.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 1,
                'uraian' => 'SPI BLU melaksanakan audit kinerja.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 2,
                'uraian' => 'IAC memuat mandat melakukan audit kinerja.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 2,
                'uraian' => 'Pedoman/Petunjuk pelaksanaan audit kinerja 
                yang memuat minimal perencanaan audit, pelaksanaan audit dan pengkomunikasian hasil audit.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 2,
                'uraian' => 'SPI BLU melaksanakan audit kinerja dengan SDM yang memiliki kompetensi.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Perencanaan audit kinerja telah: dikomunikasikan kepada stakeholder/auditee/manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Perencanaan audit kinerja telah: mempertimbangkan pemahaman proses bisnis 
                sasaran/program/kegiatan yang diaudit.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Perencanaan audit kinerja telah: mengidentifikasi dan menilai risiko strategis dan 
                risiko operasional terkait sasaran/program/kegiatan yang diaudit.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Perencanaan audit kinerja telah: menentukan tujuan, ruang lingkup, dan 
                kriteria (penetapan indikator kinerja dan bobot) yang disepakati.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Perencanaan audit kinerja telah: mengembangkan Program Kerja Audit.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit kinerja telah: dilakukan oleh SDM yang 
                memiliki kompetensi terkait audit kinerja.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit kinerja telah:mengidentifikasi 
                dan menganalisis risiko utama dan efektivitas pengendalian.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit kinerja telah: mengidentifikasi kinerja yang tidak 
                optimal dan penyebab tidak optimalnya capaian kinerja tersebut.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit kinerja telah: mendokumentasikan 
                prosedur dan hasilnya dalam Kertas Kerja Audit (KKA).'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit kinerja telah: melalui supervisi dan reviu berjenjang.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Pelaksanaan audit kinerja telah: membuat simpulan dan menyusun rekomendasi.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Hasil audit kinerja telah: dikomunikasikan kepada manajemen K/L/D melalui laporan hasil audit kinerja'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Hasil audit kinerja telah: didukung prosedur untuk memonitor tindak lanjut rekomendasi hasil audit serta bukti pelaksanaan tindak lanjut.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 3,
                'uraian' => 'Kualitas Pengawasan'
            ],
            [
                'id_management_topic' => 16,
                'level' => 4,
                'uraian' => 'Audit kinerja telah dilaksanakan secara berkelanjutan (terinternalisasi).'
            ],
            [
                'id_management_topic' => 16,
                'level' => 4,
                'uraian' => 'Pedoman dan pelaksanaan audit kinerja telah dievaluasi dan 
                disesuaikan secara terus menerus sesuai kebutuhan dan perubahan lingkungan strategis.'
            ],
            [
                'id_management_topic' => 16,
                'level' => 4,
                'uraian' => 'Audit kinerja telah menghasilkan kualitas pengawasan yang konsisten dan 
                berkelanjutan dalam rangka perbaikan GRC (contoh: hasil audit kinerja telah 
                terintegrasi dengan perbaikan tata kelola, manajemen risiko dan pengendalian internal organisasi).'
            ],
            [
                'id_management_topic' => 16,
                'level' => 5,
                'uraian' => 'SPI BLU melakukan inovasi dalam praktik pengawasan audit kinerja yang adaptif 
                terhadap perubahan lingkungan strategis. Hasil pengawasan kinerja memberikan foresight dan 
                keyakinan bagi manajemen K/L/D dalam mencapai tujuan organisasi.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 1,
                'uraian' => 'Terdapat Internal Audit Charter (IAC) atau dokumen lain yang dipersamakan.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 1,
                'uraian' => 'Terdapat Pedoman/Petunjuk Pelaksanaan pemberian asurans atas efektivitas GRC.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 1,
                'uraian' => 'SPI BLU melaksanakan asurans atas efektivitas proses tata kelola, manajemen 
                risiko dan pengendalian intern.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 2,
                'uraian' => 'IAC memuat mandat untuk melakukan asurans atas efektivitas proses 
                tata kelola, manajemen risiko dan pengendalian (GRC).'
            ],
            [
                'id_management_topic' => 17,
                'level' => 2,
                'uraian' => 'Pedoman/Petunjuk pelaksanaan asurans atas GRC yang memuat minimal 
                persiapan asurans, pelaksanaan asurans dan pelaporan asurans.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 2,
                'uraian' => 'SPI BLU melaksanakan asurans atas efektivitas proses tata kelola, 
                manajemen risiko dan pengendalian intern dengan SDM yang memiliki kompetensi.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Persiapan asurans atas GRC telah: mempertimbangkan proses bisnis serta 
                kompleksitas unit kerja dalam organisasi.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Persiapan asurans atas GRC telah: dilakukan oleh SDM yang 
                memiliki kompetensi terkait asurans atas GRC.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Persiapan asurans atas GRC telah: mengidentifikasi 
                objek asurans berdasarkan sasaran strategis  organisasi.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Persiapan asurans atas GRC telah: menentukan tujuan, 
                ruang lingkup, metodologi, tahapan dan jadwal waktu, sistematika pelaporan, 
                rencana kebutuhan sumber daya serta susunan tim asurans.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Persiapan asurans atas GRC telah: mengembangkan Program Kerja Asurans.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: dikomunikasikan kepada stakeholder/auditee/manajemen K/L/D.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: mengevaluasi informasi/bukti audit yang diperoleh.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: menilai kualitas sasaran strategis
                 dan strategi pencapaian sasaran strategis.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: menilai struktur dan proses unsur-unsur manajemen risiko.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: menilai pencapaian tujuan organisasi yaitu efektivitas 
                dan efisiensi pencapaian tujuan organisasi; keandalan pelaporan keuangan; pengamanan aset negara; dan 
                ketaatan terhadap peraturan perundang-undangan. '
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: mempertimbangkan kejadian penyimpangan/fraud/korupsi 
                yang mempengaruhi GRC.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: mendokumentasikan prosedur dan hasilnya dalam 
                Kertas Kerja Audit (KKA).'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: melalui supervisi dan reviu berjenjang.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Pelaksanaan asurans atas GRC telah: memberikan opini/simpulan terhadap 
                efektivitas GRC organisasi K/L/D dan memberikan rekomendasi perbaikan.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Hasil asurans atas GRC telah: dikomunikasikan kepada manajemen K/L/D 
                melalui laporan hasil asurans.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Hasil asurans atas GRC telah: didukung prosedur untuk memonitor 
                tindak lanjut rekomendasi hasil asurans serta bukti pelaksanaan tindak lanjut.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 3,
                'uraian' => 'Kualitas Pengawasan'
            ],
            [
                'id_management_topic' => 17,
                'level' => 4,
                'uraian' => 'Asurans atas GRC telah dilaksanakan secara berkelanjutan (terinternalisasi).'
            ],
            [
                'id_management_topic' => 17,
                'level' => 4,
                'uraian' => 'Pedoman dan pelaksanaan asurans atas GRC telah dievaluasi dan disesuaikan 
                secara terus menerus sesuai kebutuhan dan perubahan lingkungan strategis.'
            ],
            [
                'id_management_topic' => 17,
                'level' => 4,
                'uraian' => 'Hasil asurans atas GRC telah menghasilkan kualitas pengawasan yang 
                konsisten dan berkelanjutan dalam rangka perbaikan GRC (contoh: hasil asurans atas GRC 
                telah diarahkan untuk memitigasi risiko strategis organisasi).'
            ],
            [
                'id_management_topic' => 17,
                'level' => 5,
                'uraian' => 'SPI BLU melakukan inovasi dalam praktik asurans atas GRC yang adaptif 
                terhadap perubahan lingkungan strategis. Hasil asurans atas GRC memberikan foresight 
                dan keyakinan bagi manajemen K/L/D dalam memastikan tata kelola, manajemen risiko dan
                 pengendalian internal organisasi telah termanifestasi secara optimum dalam penyelenggaraan 
                 pemerintahan.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 1,
                'uraian' => 'Terdapat Internal Audit Charter (IAC) atau dokumen lain yang dipersamakan.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 1,
                'uraian' => 'Terdapat Pedoman/Petunjuk Pelaksanaan/SOP jasa konsultansi.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 1,
                'uraian' => 'SPI BLU memberikan jasa konsultansi.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 2,
                'uraian' => 'IAC memuat kewenangan SPI BLU untuk melakukan layanan konsultansi
                 dan jenis jasa konsultansi yang diharapkan oleh organisasi.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 2,
                'uraian' => 'Pedoman/Petunjuk Pelaksanaan jasa konsultansi mencakup minimal 
                metodologi, komunikasi dengan auditi, pernyataan tanggung jawab, dan pengkomunikasian hasil jasa konsultansi.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 2,
                'uraian' => 'SPI BLU memberikan jasa konsultansi dengan SDM yang memiliki kompetensi audit internal 
                atau jasa konsultansi yang relevan.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 3,
                'uraian' => 'SPI BLU memberikan jasa konsultansi sesuai kewenangan dalam IAC dan pedoman/petunjuk 
                pelaksanaan jasa konsultansi, dengan: menetapkan metodologi dan jenis jasa konsultansi (misalnya 
                apakah dikombinasikan dengan penugasan asurans atau dilakukan terpisah).'
            ],
            [
                'id_management_topic' => 18,
                'level' => 3,
                'uraian' => 'SPI BLU memberikan jasa konsultansi sesuai kewenangan dalam IAC dan pedoman/petunjuk 
                pelaksanaan jasa konsultansi, dengan: berkomunikasi dengan mitra kerja dan menyepakati prinsip 
                dan pendekatan yang akan digunakan oleh SPI BLU dalam melakukan dan melaporkan jasa konsultansi.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 3,
                'uraian' => 'SPI BLU memberikan jasa konsultansi sesuai kewenangan dalam IAC dan pedoman/petunjuk 
                pelaksanaan jasa konsultansi, dengan: terbebas dari hal-hal yang dapat mengganggu independensi 
                dan objektivitas.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 3,
                'uraian' => 'SPI BLU memberikan jasa konsultansi sesuai kewenangan dalam IAC dan pedoman/petunjuk 
                pelaksanaan jasa konsultansi, dengan: mendapatkan jaminan bahwa mitra kerja akan bertanggung 
                jawab atas keputusan dan/atau tindakan yang diambil sebagai hasil dari saran yang diberikan 
                melalui jasa konsultansi.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 3,
                'uraian' => 'SPI BLU memberikan jasa konsultansi sesuai kewenangan dalam IAC dan pedoman/petunjuk 
                pelaksanaan jasa konsultansi, dengan: dilakukan oleh SDM yang memiliki kompetensi audit internal 
                atau jasa konsultansi yang relevan serta dilakukan secara due profesional care.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 3,
                'uraian' => 'Hasil jasa konsultansi yang diberikan SPI BLU telah dikomunikasikan kepada manajemen 
                K/L/D melalui laporan hasil jasa konsultansi.	'
            ],
            [
                'id_management_topic' => 18,
                'level' => 3,
                'uraian' => 'Melaporkan kepada pimpinan K/L/D bila ada hasil dari kegiatan jasa konsultansi 
                memiliki risiko (sifat dan materilitasnya) yang signifikan terhadap organisasi.	'
            ],
            [
                'id_management_topic' => 18,
                'level' => 3,
                'uraian' => 'Kualitas Pengawasan'
            ],
            [
                'id_management_topic' => 18,
                'level' => 4,
                'uraian' => 'Pemberian jasa konsultansi telah dilaksanakan secara berkelanjutan (terinternalisasi).'
            ],
            [
                'id_management_topic' => 18,
                'level' => 4,
                'uraian' => 'Pedoman dan pelaksanaan pemberian jasa konsultansi telah dievaluasi dan 
                disesuaikan secara terus menerus sesuai kebutuhan.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 4,
                'uraian' => 'Manajemen menjadikan SPI BLU sebagai mitra strategis (strategic partner)
                 dengan menerima dan menggunakan pengetahuan SPI BLU untuk meningkatkan kualitas GRC dan membantu mencapai tujuan organisasi.'
            ],
            [
                'id_management_topic' => 18,
                'level' => 5,
                'uraian' => 'SPI BLU memberikan inovasi dalam praktik jasa konsultansi yang adaptif terhadap perubahan lingkungan
                 strategis. Hasil jasa konsultansi memberikan foresight dan keyakinan bagi manajemen K/L/D dalam memberikan
                  informasi peluang dan memanfaatkan peluang tersebut bagi peningkatan nilai tambah organisasi.'
            ],
        ];
        foreach($uraian as $key => $value){
            Uraian::create($value);
        }
    }
}
