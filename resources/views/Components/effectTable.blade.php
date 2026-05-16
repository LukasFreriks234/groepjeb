<div>
    <h2>Effects</h2>
    <ul id="effectsList">
       @foreach ($categories as $category)
            <li>{{ $category->category}}</li>
        @endforeach
    </ul>
</div>

