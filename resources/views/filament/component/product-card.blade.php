<x-layouts.base>
    @php
        $product = $getRecord();
    @endphp
    <p>{{ $product }}</p>
</x-layouts.base>