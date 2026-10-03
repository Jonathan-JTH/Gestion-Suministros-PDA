@php
    $brandBgPath = public_path('images/fabrigas-planta.png');
    $brandBgUrl = is_file($brandBgPath) ? asset('images/fabrigas-planta.png') : null;
@endphp
@if($brandBgUrl)
    <div class="brand-background" style="--brand-bg-image: url('{{ $brandBgUrl }}');" aria-hidden="true"></div>
@endif
