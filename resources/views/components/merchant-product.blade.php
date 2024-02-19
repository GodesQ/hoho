<div>
    <div class="mb-1" style="font-weight: 600;">
        <i class="bx bx-package" style="color: #a7a7a7; margin-right: 3px;"></i>
        {{ $product->name ?? null }}
    </div>
    <div style="font-size: 12px;">
        <i class="bx bx-building" style="color: #a7a7a7; margin-right: 3px;"></i>
        {{ $product->merchant->name ?? null }}
    </div>
</div>