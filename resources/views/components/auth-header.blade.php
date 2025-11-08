 <div class="login-brand">
     <img src="{{ asset('img/LogoPolinema.png') }}"
         alt="logo"
         width="100"
         class="shadow-light rounded-circle">
         @if (Request::is('register'))
         <h1><strong><span class="text-warning">Register</span></strong><span class="text-primary">SPI</span></h1>
         @elseif (Request::is('login'))
         <h1><strong><span class="text-warning">Login</span></strong><span class="text-primary">SPI</span></h1>
         @endif
         <h2>Politeknik Negeri Malang</h2>
 </div>
