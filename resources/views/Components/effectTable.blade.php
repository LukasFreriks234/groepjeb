@props(['categories', 'effectTotals' => [], 'qualityOfLife' => 0])

<div>
    <h2>Effects</h2>

    <ul 
        id="effectsList"
        tabindex="0"
        aria-label="Effect totals: @foreach($categories as $category){{ $category->category }}: {{ $effectTotals[$category->category] ?? 0 }}, @endforeach Quality of Life: {{ $qualityOfLife }}"
    >
        @foreach ($categories as $category)
            <li>
                {{ $category->category }}:
                <span data-effect-category="{{ $category->category }}">
                    {{ $effectTotals[$category->category] ?? 0 }}
                </span>
            </li>
        @endforeach

        <li>
            Quality of Life:
            <span id="qualityOfLifeValue">{{ $qualityOfLife }}</span>
        </li>
    </ul>
</div>