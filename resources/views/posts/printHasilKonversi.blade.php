<!DOCTYPE html>
<html>
<head>
    <title>Print Hasil Konversi</title>
</head>
<body>
    <h1>Detail Tugas</h1>
    <p>Waktu: {{ $post->waktu }}</p>
    <p>Tempat: {{ $post->tempat }}</p>
    <p>Anggota: {{ $post->anggota }}</p>
    <p>Jenis: {{ $post->jenis }}</p>
    <p>Judul: {{ $post->judul }}</p>
    <p>Deskripsi Tugas: {{ $post->deskripsi }}</p>
    <p>Bidang: {{ $post->bidang }}</p>
    <p>Penanggung Jawab: {{ $post->tanggungjawab }}</p>
    <!-- Tambahkan konten lainnya sesuai kebutuhan -->
</body>
</html>
