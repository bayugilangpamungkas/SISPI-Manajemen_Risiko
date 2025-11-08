@extends('layout.master')
@section('title','reviewLaporan')

@section('content')

<div id="modal-master" class="modal-dialog modal-xl" role="document">
    <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Berita</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body p-0">
            <table class="table table-striped table-sm">
                @forelse ($posts as $post)
                <tr>
                    <th class="text-right">Prodi : </th><td>{{ ($post->waktu)? $post->waktu : '' }}</td>
                    <th class="text-right">Oleh : </th><td>{{ $post->jenis }}</td>
                </tr>
                <tr>
                    <th class="text-right">Oleh : </th><td>{{ $post->jenis }}</td>
                </tr>
                <tr>
                    <th class="text-right">Kategori : </th><td>{{ $post->judul }}</td>
                    <th class="text-right">Tanggal : </th><td>{{ $post->bidang }}</td>
                </tr>
                <tr>
                    <th colspan="4" class="text-center"><h4>{{ $post->bidang }}</h4></th>
                </tr>
                <tr>
                    <td colspan="4" class="text-justify p-3 p-md-4">{!! $post->judul !!}</td>
                </tr>
            </table>
        </div>
        @empty
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-warning">Keluar</button>
        </div>
        @endforelse

    </div>
</div>

<script>
    $(document).ready(function() {
        unblockUI();
    });
</script>


@endsection
