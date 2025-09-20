<x-layouts.base>
    @php
        $product = $getRecord();
    @endphp
    
    <div class="overflow-hidden">
        <!-- Banner -->
        <img src="{{ $product->images 
            ? Storage::disk('public')->url($product->images) 
            : asset('products/no-image.png') }}"
        alt="{{ $product->name }}"
        class="w-full h-40 object-cover">

        <!-- Content -->
        <div class="p-4 space-y-2">
            <h3 class="text-lg font-semibold" style="color: var(--color-text-heading)">
                {{ $product->name }}
            </h3>

            <h3 class="text-sm line-clamp-2" style="color: var(--color-text-body)">
                {{ $product->productType->name }}
            </h3>

            <div class="flex justify-between items-center mt-3 space-x-2">
                {{-- Harga setelah diskon --}}
                <span class="text-lg font-bold text-green-600">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</x-layouts.base>