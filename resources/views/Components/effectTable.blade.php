@props(['categories', 'effectTotals' => []])

<div>
    <h2>Effects</h2>

    <ul id="effectsList">
        @foreach ($categories as $category)
            <li>
                {{ $category->category }}:
                <span data-effect-category="{{ $category->category }}">
                    {{ $effectTotals[$category->category] ?? 0 }}
                </span>
            </li>
        @endforeach
    </ul>
</div>