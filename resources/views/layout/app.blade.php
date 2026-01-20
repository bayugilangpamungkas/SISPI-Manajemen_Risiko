<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title')</title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @stack('style')

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            @includeWhen(!($isWelcomePage ?? false), 'components.header')
            @includeWhen(!($isWelcomePage ?? false), 'components.sidebar')
            @yield('main')
            @include('components.footer')
        </div>
    </div>

    <!-- General JS Scripts - Correct Order -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Your other scripts -->
    <script src="{{ asset('js/stisla.js') }}">
        < /scrip> <
        script src = "https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js" >
    </script>

    @stack('scripts')

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Dropdown Menu Handler -->
    <script>
        $(document).ready(function() {
            // Handle dropdown menu with clickable parent link
            $('.sidebar-menu .dropdown > .has-dropdown').each(function() {
                var $this = $(this);
                var $parent = $this.parent('li.dropdown');
                var $dropdownMenu = $parent.find('.dropdown-menu');

                // Prevent default dropdown behavior
                $this.off('click');

                // Click on the entire menu link
                $this.on('click', function(e) {
                    e.preventDefault();

                    // Check if dropdown is already open
                    if ($parent.hasClass('active')) {
                        // If open, navigate to the link
                        var href = $this.attr('href');
                        if (href && href !== '#') {
                            window.location.href = href;
                        }
                    } else {
                        // If closed, open the dropdown
                        // Close other dropdowns
                        $('.sidebar-menu .dropdown').not($parent).removeClass('active');
                        $('.sidebar-menu .dropdown .dropdown-menu').not($dropdownMenu).slideUp(200);

                        // Open current dropdown
                        $parent.addClass('active');
                        $dropdownMenu.slideDown(200);
                    }
                });
            });
        });
    </script>

</body>

</html>
