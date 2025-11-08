<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('style/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('fontawesome-free/css/all.min.css') }}"

    <title>SPI POLINEMA</title>
  </head>
  <body onload="initClock()">
    <nav class="navbar navbar-expand-lg navbar-light bg-dark fixed-top">
        <a class="navbar-brand text-white" href="#"><b>SPI POLINEMA</b></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item ml-5 pl-5">
                <a class="nav-link text-white">
                <span id="dayname">Day</span>,
                <span id="daynum">00</span>
                <span id="month">Month</span>
                <span id="year">Year</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white">
                <span id="hour">00</span>:
                <span id="minutes">00</span>:
                <span id="seconds">00</span>
                <span id="period">AM</span>
                </a>
            </li>

          </ul>

          <div class="icon ml-auto">
            <h5>
                <i class="fa-solid fa-user mr-3" style="color: #ffffff;"></i>
                <a class="navbar-brand text-white" href="#">Admin</a>
            </h5>
        </div>
      </nav>

    <div class="row no-gutters mt-4 fixed">
        <div class="col-md-2 bg-dark mt-2 pr-3 pt-4">
            <ul class="nav flex-column ml-3 mb-5">
                <li class="nav-item">
                    <div class="info">
                        <a href="#" class="d-block text-white">
                            {{-- {{ $user->name }} --}}
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <hr class="mt-auto bg-secondary">
                  <a class="nav-link active text-white" href="/dashboard"><i class="fa-solid fa-gauge mr-2" style="color: #e6e6e6;"></i>
                    Dashboard</a>
                  <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="/posts"><i class="fa-solid fa-list-check mr-2" style="color: #e6e6e6;"></i>
                    Review Laporan Keuangan</a>
                  <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="#"><i class="fa-solid fa-list-check mr-2" style="color: #e6e6e6;"></i>
                    Laporan Unit Kerja</a>
                  <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="#"><i class="fa-solid fa-list-check mr-2" style="color: #e6e6e6;"></i>
                    Audit Eksternal</a>
                  <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="#"><i class="fa-solid fa-list-check mr-2" style="color: #e6e6e6;"></i>
                    Laporan Hasil Pemeriksaan</a>
                  <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="#"><i class="fa-solid fa-list-check mr-2" style="color: #e6e6e6;"></i>
                    Klarifikasi Hasil Review</a>
                  <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/anggotas"><i class="fa-solid fa-user mr-2" style="color: #e6e6e6;"></i>
                      Anggota</a>
                    <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/audites"><i class="fa-regular fa-user mr-2" style="color: #e6e6e6;"></i>
                      Auditee</a>
                    <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="/dokumens"><i class="fa-solid fa-file mr-2" style="color: #e6e6e6;"></i>
                      Dokumen</a>
                    <hr class="bg-secondary mt-auto mb-auto">
                </li>
                <li class="nav-item">
                  <a class="nav-link text-white" href="/logout"><i class="fa-solid fa-right-from-bracket mr-2" style="color: #e6e6e6;"></i>
                    Logout</a>
                  <hr class="bg-secondary mt-auto mb-auto">
                </li>
              </ul>
        </div>

        @section('content')

        @show
    </div>
    <footer>
        <nav class="navbar navbar-expand-lg navbar-light bg-dark">
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav ml-auto mr-auto">
                <li class="nav-items">
                    <a class="nav-link text-white">
                    Copyright © 2024
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white">
                    <b>Satuan Pengawas Internal - Politeknik Negeri Malang</b>
                    </a>
                </li>

              </ul>

              <div class="icon ml-auto">
                <h5>
                    <a class="navbar-brand text-white"><b>SPI POLINEMA</b></a>
                </h5>
            </div>
          </nav>
    </footer>



    <!-- Optional JavaScript -->
     <!-- Date Time -->
     <script type="text/javascript">
     function updateClock(){
        var now = new Date();
        var dname = now.getDay(),
            mo = now.getMonth(),
            dnum = now.getDate(),
            yr = now.getFullYear(),
            hou = now.getHours(),
            min = now.getMinutes(),
            sec = now.getSeconds(),
            pe = "AM";

            if(hou == 0){
                hou = 12;
            }
            if(hou > 12){
                hou = hou - 12;
                pe = "PM";
            }

            Number.prototype.pad = function(digits){
                for(var n = this.toString(); n.length < digits; n = 0 + n);
                return n;
            }

            var months = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
            var week = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
            var ids = ["dayname","month","daynum","year","hour","minutes","seconds","period"];
            var values = [week[dname], months[mo], dnum.pad(2), yr, hou.pad(2), min.pad(2), sec.pad(2), pe];
            for(var i = 0; i < ids.length; i++)
            document.getElementById(ids[i]).firstChild.nodeValue = values[i];
     }
     function initClock(){
        updateClock();
        window.setInterval("updateClock()", 1);
     }
     </script>
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{  asset('js/admin.js') }}"></script>
</body>
</html>
