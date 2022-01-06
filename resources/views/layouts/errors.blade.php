@if (count(errors()) > 0)
    <link rel="stylesheet" href="{{ mix('iziToast/css/iziToast.min.css') }}">
    <script src="{{ mix('iziToast/js/iziToast.min.js') }}"></script>
    @foreach (errors() as $error)
        <script>
            iziToast.show({
                title: '有错误发生!',
                message: '{{ $error }}',
                color: '#dc3545',
                position: 'topRight',
                messageColor: '#ffffff',
                titleColor: '#ffffff'
            });
        </script>
    @endforeach
@endif
