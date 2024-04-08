@if ($merchant)
    <div>
        <div class="mb-1" style="font-weight: 600;">
            <i class="bx bx-store" style="color: #a7a7a7; margin-right: 3px;"></i>
            {{ (strlen($merchant->name) >= 25 ? substr($merchant->name, 0, 25) . '...' : $merchant->name) ?? null }}
        </div>
        <div style="font-size: 12px;">
            <i class="bx bx-info-square" style="color: #a7a7a7; margin-right: 3px;"></i>
            {{ $merchant->type ?? null }}
        </div>
    </div>
@else
    <span>-</span>
@endif
