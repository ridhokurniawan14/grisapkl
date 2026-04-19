@php
    $lat = $getRecord()->latitude;
    $lng = $getRecord()->longitude;
    $radius = $getRecord()->radius ?? 50;
    $uniqueId = 'map-' . $getRecord()->id;
@endphp

@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endonce

<div wire:ignore x-data="{}" x-init="$nextTick(() => {
    const el = document.getElementById('{{ $uniqueId }}');
    if (!el || el._leaflet_id) return;

    const map = L.map('{{ $uniqueId }}', {
        zoomControl: false,
        attributionControl: true,
    }).setView([{{ $lat }}, {{ $lng }}], 17);

    L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&hl=id&x={x}&y={y}&z={z}', {
        subdomains: ['0', '1', '2', '3'],
        maxZoom: 21,
        attribution: '© Google Maps',
    }).addTo(map);

    L.control.zoom({ position: 'topright' }).addTo(map);

    const redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
    });

    L.marker([{{ $lat }}, {{ $lng }}], { icon: redIcon })
        .addTo(map)
        .bindPopup('<b>Titik Absensi</b><br>Radius: {{ $radius }} meter')
        .openPopup();

    L.circle([{{ $lat }}, {{ $lng }}], {
        color: '#ef4444',
        fillColor: '#fca5a5',
        fillOpacity: 0.35,
        radius: {{ $radius }},
        weight: 2,
    }).addTo(map);

    // Triple invalidate — handle tab hidden transition
    [100, 500, 1000].forEach(ms => setTimeout(() => map.invalidateSize(), ms));
});">
    <div id="{{ $uniqueId }}" style="height: 450px; width: 100%; border-radius: 12px;"></div>
</div>
