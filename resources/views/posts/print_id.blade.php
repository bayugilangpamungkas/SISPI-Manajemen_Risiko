
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Print Review</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('/') }}plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('/') }}dist/css/adminlte.min.css">
</head>
<body>
<div class="wrapper">
  <!-- Main content -->
  <section class="invoice">
    <!-- title row -->
    <div class="row">
      <div class="col-12">
        <h2 class="page-header">
          <i class="fas fa-globe"></i> Laporan Review
          <small class="float-right">Date: {{ date('d-M-Y') }}</small>
        </h2>
      </div>
      <!-- /.col -->
    </div>
    <!-- info row -->

    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
      <div class="col-12 table-responsive">
        <table class="table table-white table-sm">
            @foreach ($posts as $posts )
            <tr>
                <th class="text-right">Waktu : </th><td><i class="fa-regular fa-calendar-days mr-1" style="color: #0050db;"></i>
                    {{ ($posts->waktu)? $posts->waktu : '' }}</td>
            </tr>

            <tr>
                <th class="text-right">Tempat : </th><td><i class="fa-regular fa-building mr-1" style="color: #0050db;"></i>{{ $posts->tempat }}</td>
            </tr>
            <tr>
                <th class="text-right">Anggota : </th>
                <td>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="text-center">
                                <th scope="col">No</th>
                                <th scope="col">Nama Anggota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>{{ $posts->anggota }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <th class="text-right">Jenis : </th><td><a class="bg-primary p-1 rounded text-white">{{$posts->jenis }}</a></td>
            </tr>
            <tr>
                <th class="text-right">Judul : </th><td>{{ $posts->judul }}</td>
            </tr>
            <tr>
                <th class="text-right">Deskripsi Tugas : </th><td>{{ $posts->deskripsi }}</td>
            </tr>
            <tr>
                <th class="text-right">Bidang : </th><td>{{ $posts->bidang }}</td>
            </tr>
            <tr>
                <th class="text-right">Penanggung Jawab : </th><td>{{ $posts->tanggungjawab }}</td>
            </tr>

            <tr>
                <th class="text-right">Dokumen : </th>
                <td>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th scope="col">No</th>
                                <th scope="col">Nama Berkas</th>
                                <th scope="col">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>{{ $posts->dokumen }}</td>
                                <td>File Tugas</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endforeach
        </table>
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->


    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
<!-- Page specific script -->
<script>
  window.addEventListener("load", window.print());
</script>
</body>
</html>
