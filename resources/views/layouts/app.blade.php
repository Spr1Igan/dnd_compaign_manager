<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'D&D Campaign Manager')</title>

    @vite('resources/css/app.css')
</head>

<body>

    @include('partials.header')

    <main class="page-content">
        @yield('content')
    </main>

    @stack('modals')

    @include('partials.footer')


    <script>
        const profileButton = document.getElementById('profileButton');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileButton && profileDropdown) {
            profileButton.addEventListener('click', function (event) {
                event.stopPropagation();
                profileDropdown.classList.toggle('open');
            });

            document.addEventListener('click', function () {
                profileDropdown.classList.remove('open');
            });
        }

        document.querySelectorAll('[data-confirm-target]').forEach(function (trigger) {
            const dialog = document.querySelector(
                `[data-confirm-dialog="${trigger.dataset.confirmTarget}"]`
            );

            if (!dialog) {
                return;
            }

            const form = trigger.closest('form');
            const cancelButtons = dialog.querySelectorAll('[data-confirm-cancel]');
            const confirmButton = dialog.querySelector('[data-confirm-submit]');

            const closeDialog = function () {
                dialog.setAttribute('hidden', '');
                trigger.focus();
            };

            trigger.addEventListener('click', function () {
                dialog.removeAttribute('hidden');
                confirmButton?.focus();
            });

            cancelButtons.forEach(function (button) {
                button.addEventListener('click', closeDialog);
            });

            confirmButton?.addEventListener('click', function () {
                if (!form) {
                    return;
                }

                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                    return;
                }

                form.submit();
            });

            dialog.addEventListener('click', function (event) {
                if (event.target === dialog) {
                    closeDialog();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !dialog.hasAttribute('hidden')) {
                    closeDialog();
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
