<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ManagementPengawasan;

class ManagementPengawasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pengawasan = [
            [
                'id_management_uraian' => 180,
                'kualitas_pengawasan' => 'Result 1: Keyakinan yang Memadai atas Ketaatan dan 3E',
                'aktivitas_pengawasan' => 'Audit Ketaatan',
                'parameter' => 'Temuan dalam laporan hasil pengawasan ketaatan SPI BLU.',
                'sub_parameter' => 'Temuan atas ketidaktaatan terhadap peraturan/ketentuan/prosedur.',
                'cara_pengukuran' => '(Hitung jumlah butir temuan ketidaktaatan terhadap peraturan/ketentuan/
                prosedur yang telah teridentifikasi dalam laporan SPI BLU selama periode penilaian).',
            ],
            [
                'id_management_uraian' => 180,
                'kualitas_pengawasan' => 'Result 1: Keyakinan yang Memadai atas Ketaatan dan 3E',
                'aktivitas_pengawasan' => 'Audit Ketaatan',
                'parameter' => 'Temuan dalam laporan hasil pengawasan ketaatan SPI BLU.',
                'sub_parameter' => 'Nilai penyelamatan dan potensi kerugian keuangan negara/daerah.',
                'cara_pengukuran' => '(Hitung nilai penyelamatan dan potensi kerugian keuangan negara/daerah 
                selama periode penilaian).',
            ],
            [
                'id_management_uraian' => 180,
                'kualitas_pengawasan' => 'Result 1: Keyakinan yang Memadai atas Ketaatan dan 3E',
                'aktivitas_pengawasan' => 'Audit Ketaatan',
                'parameter' => 'Tindak lanjut rekomendasi atas temuan ketidaktaatan.',
                'sub_parameter' => 'Seluruh rekomendasi atas temuan ketidaktaatan yang ditindaklanjuti.',
                'cara_pengukuran' => '(Hitung persentase jumlah TL atas rekomendasi ketidaktaatan pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 180,
                'kualitas_pengawasan' => 'Result 1: Keyakinan yang Memadai atas Ketaatan dan 3E',
                'aktivitas_pengawasan' => 'Audit Ketaatan',
                'parameter' => 'Hasil pengawasan ketaatan dimanfaatkan oleh manajemen K/L/D dan stakeholders lainnya.',
                'sub_parameter' => 'Hasil pengawasan ketaatan yang dilakukan SPI BLU dimanfaatkan oleh manajemen K/L/D 
                dan stakeholders dalam pengambilan keputusan atau penyusunan kebijakan.',
                'cara_pengukuran' => '(Hitung jumlah pemanfaatan hasil pengawasan ketaatan oleh manajemen K/L/D dan 
                stakeholders pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 204,
                'kualitas_pengawasan' => 'Result 1: Keyakinan yang Memadai atas Ketaatan dan 3E',
                'aktivitas_pengawasan' => 'Audit Kinerja',
                'parameter' => 'Temuan dalam laporan hasil audit kinerja SPI BLU',
                'sub_parameter' => 'Temuan atas 3E dalam LHA  Kinerja.',
                'cara_pengukuran' => '(Hitung jumlah temuan kinerja (3E) yang telah teridentifikasi dalam Laporan 
                Hasil Audit Kinerja pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 204,
                'kualitas_pengawasan' => 'Result 1: Keyakinan yang Memadai atas Ketaatan dan 3E',
                'aktivitas_pengawasan' => 'Audit Kinerja',
                'parameter' => 'Tindak lanjut atas rekomendasi kinerja dalam Laporan Hasil Audit Kinerja.',
                'sub_parameter' => 'Seluruh rekomendasi atas temuan hasil audit kinerja yang ditindaklanjuti.',
                'cara_pengukuran' => '(Hitung persentase jumlah TL atas rekomendasi hasil audit kinerja pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 204,
                'kualitas_pengawasan' => 'Result 1: Keyakinan yang Memadai atas Ketaatan dan 3E',
                'aktivitas_pengawasan' => 'Audit Kinerja',
                'parameter' => 'Hasil pengawasan kinerja dimanfaatkan oleh stakeholders.',
                'sub_parameter' => 'Hasil pengawasan kinerja yang dilakukan SPI BLU dimanfaatkan oleh manajemen K/L/D dan 
                stakeholders dalam pengambilan keputusan atau penyusunan kebijakan.',
                'cara_pengukuran' => '(Hitung jumlah pemanfaatan hasil pengawasan kinerja oleh stakeholders pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 231,
                'kualitas_pengawasan' => 'Result 3: Memelihara dan meningkatkan kualitas tata kelola',
                'aktivitas_pengawasan' => 'Asurans atas tata kelola, manajemen risiko, dan pengendalian organisasi K/L/D',
                'parameter' => 'Integrasi hasil asurans GRC.',
                'sub_parameter' => 'Hasil penilaian atau kesimpulan tentang penugasan yang diberikan oleh Pimpinan SPI BLU secara 
                menyeluruh memberikan tinjauan proses tata kelola, pengelolaan risiko, dan/atau pengendalian organisasi.',
                'cara_pengukuran' => '(Laporan hasil asurans GRC yang berisi opini atas proses tata kelola, pengelolaan risiko, 
                dan/atau pengendalian organisasi).',
            ],
            [
                'id_management_uraian' => 231,
                'kualitas_pengawasan' => 'Result 3: Memelihara dan meningkatkan kualitas tata kelola',
                'aktivitas_pengawasan' => 'Asurans atas tata kelola, manajemen risiko, dan pengendalian organisasi K/L/D',
                'parameter' => 'Temuan atas tata kelola, manajemen risiko dan pengendalian internal pada Laporan Hasil Asurans GRC.',
                'sub_parameter' => 'Temuan hasil asurans atas tata kelola, manajemen risiko dan pengendalian internal.',
                'cara_pengukuran' => '(Hitung jumlah butir temuan atas tata kelola, manajemen risiko dan pengendalian internal pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 231,
                'kualitas_pengawasan' => 'Result 3: Memelihara dan meningkatkan kualitas tata kelola',
                'aktivitas_pengawasan' => 'Asurans atas tata kelola, manajemen risiko, dan pengendalian organisasi K/L/D',
                'parameter' => 'Tindak lanjut rekomendasi oleh  manajemen atas  saran  hasil pengawasan perbaikan GRC.',
                'sub_parameter' => 'Seluruh rekomendasi SPI BLU atas  saran  hasil pengawasan perbaikan GRC telah ditindaklanjuti oleh manajemen.',
                'cara_pengukuran' => '(Hitung jumlah persentase rekomendasi oleh  SPI BLU atas  saran hasil pengawasan perbaikan GRC yang telah ditindaklanjuti oleh manajemen pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 231,
                'kualitas_pengawasan' => 'Result 3: Memelihara dan meningkatkan kualitas tata kelola',
                'aktivitas_pengawasan' => 'Asurans atas tata kelola, manajemen risiko, dan pengendalian organisasi K/L/D',
                'parameter' => 'Hasil asurans GRC dimanfaatkan oleh stakeholders.',
                'sub_parameter' => 'Hasil asurans GRC yang dilakukan SPI BLU dimanfaatkan oleh manajemen K/L/D dan stakeholders dalam pengambilan keputusan atau penyusunan kebijakan.',
                'cara_pengukuran' => '(Hitung jumlah pemanfaatan hasil asurans GRC oleh stakeholders pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 249,
                'kualitas_pengawasan' => 'Result 2 : Early Warning dan Peningkatan Efektivitas MR',
                'aktivitas_pengawasan' => 'Jasa Konsultansi',
                'parameter' => 'Pelaksanaan rencana aksi (renaksi) atas saran/rekomendasi hasil jasa konsultansi terkait penyajian LK, pengamanan aset dan  pengendalian permasalahan strategis.',
                'sub_parameter' => 'Renaksi atas rekomendasi hasil jasa konsultansi yang dilaksanakan oleh mitra.',
                'cara_pengukuran' => '(Jumlah renaksi atas saran/rekomendasi hasil jasa konsultansi yang dilaksanakan oleh mitra pada periode penilaian).',
            ],
            [
                'id_management_uraian' => 249,
                'kualitas_pengawasan' => 'Result 2 : Early Warning dan Peningkatan Efektivitas MR',
                'aktivitas_pengawasan' => 'Jasa Konsultansi',
                'parameter' => 'SPI BLU memberikan atensi untuk peningkatan kualitas penyajian LK, pengamanan aset dan pengendalian terjadinya permasalahan strategis.',
                'sub_parameter' => 'Atensi yang diberikan untuk  mencegah permasalahan yang berulang, permasalahan strategis dan penyimpangan (fraud).',
                'cara_pengukuran' => '(Hitung jumlah atensi yang diberikan SPI BLU kepada manajemen  untuk  mencegah permasalahan strategis terjadi pada periode penilaian).',
            ],
        ];
        foreach($pengawasan as $key => $value){
            ManagementPengawasan::create($value);
        }
    }
}
