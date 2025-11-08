@component('mail::message')
# Verifikasi Alamat Email Anda

Halo,

Terima kasih telah mendaftar. Klik tombol di bawah ini untuk memverifikasi alamat email Anda:

@component('mail::button', ['url' => $actionUrl])
Verifikasi Email
@endcomponent

Jika Anda tidak mendaftar akun ini, tidak perlu melakukan apa pun.

Terima kasih,<br>
SPI Polinema
@endcomponent
