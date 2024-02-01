<div>
    <div><i class="bx bx-map"></i> {{ substr($data->merchant->address, 0, 25) }}...</div>
    <div style="font-size: 12px;">{{ $data->merchant->latitude }}, {{ $data->merchant->longitude }}</div>
</div>