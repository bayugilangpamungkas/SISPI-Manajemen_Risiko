<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('/') }}plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('/') }}plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/') }}dist/css/adminlte.min.css">

    <style>
        body {
            background-image: url('{{ asset('img/LoginBackground.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div class="col-md-16 p-5 pt-2">
        <h3><i class="fa-regular fa-user mr-2"></i>FEEDBACK</h3>
        <hr>
        <h4 class="tittle-1">
            <span class="span0">Feedback</span>
            <span class="span1">User</span>
        </h4>
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow rounded">
                    <div class="card-body">
                        <table class="table table-white table-sm">
                            <tr>
                                Untuk mengisi kuesioner feedback aplikasi, silahkan
                                klik link di bawah ini
                            </tr>
                            <p>
                                <tr>
                                    <th class="text-left">Link : </th>
                                    <td>
                                        <a href="https://forms.gle/mahK8WtUnzsizVgw8" target="_blank">
                                            https://forms.gle/mahK8WtUnzsizVgw8
                                        </a>
                                    </td>
                                </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
