@php
    $map = [
        'pendiente' => ['class' => 'text-bg-secondary', 'label' => 'Pendiente'],
        'en_proceso' => ['class' => 'text-bg-primary', 'label' => 'En proceso'],
        'atendida' => ['class' => 'text-bg-success', 'label' => 'Atendida'],
        'rechazada' => ['class' => 'text-bg-dark', 'label' => 'Rechazada'],
    ];
    $cfg = $map[$estado] ?? ['class' => 'text-bg-secondary', 'label' => str_replace('_', ' ', $estado)];
@endphp
<span class="badge {{ $cfg['class'] }}">{{ $cfg['label'] }}</span>
