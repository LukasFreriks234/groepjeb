@props(['categories', 'effectTotals' => [], 'qualityOfLife' => 0])

@php
    $effectsText = 'Effects. ';

    foreach ($categories as $category) {
        $effectValue = $effectTotals[$category->category] ?? 0;
        $effectsText .= $category->category . ' ' . $effectValue . '. ';
    }

    $effectsText .= 'Quality of Life ' . $qualityOfLife . '.';
@endphp

<div>
    <h2>Effects</h2>

    <div
        id="effectsReader"
        class="sr-only"
        role="status"
        aria-live="polite"
        aria-atomic="true"
    ></div>

    <ul
        id="effectsList"
        tabindex="0"
        aria-describedby="effectsReader"
    >
        @foreach ($categories as $category)
            @php
                $effectValue = $effectTotals[$category->category] ?? 0;
            @endphp

            <li>
                {{ $category->category }}:
                <span data-effect-category="{{ $category->category }}">
                    {{ $effectValue }}
                </span>
            </li>
        @endforeach

        <li>
            Quality of Life:
            <span id="qualityOfLifeValue">{{ $qualityOfLife }}</span>
        </li>
    </ul>
</div>