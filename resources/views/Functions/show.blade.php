<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $function->name }}</title>
    <link href="{{ asset('css/overviewFunctionStyle.css') }}" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">
</head>

<body>

    <h1>{{ $function->name }}</h1>

    <div class="container">

        <div class="form-section">

            <p><strong>Name:</strong> {{ $function->name }}</p>

            <p><strong>Category:</strong> {{ $function->category }}</p>

            <h2>Effects</h2>

            <p><strong>Safety:</strong>
                {{ data_get($function->effects, 'Safety', 0) }}
            </p>

            <p><strong>Recreation:</strong>
                {{ data_get($function->effects, 'Recreation', 0) }}
            </p>

            <p><strong>Environmental Quality:</strong>
                {{ data_get($function->effects, 'Environmental Quality', 0) }}
            </p>

            <p><strong>Services:</strong>
                {{ data_get($function->effects, 'Services', 0) }}
            </p>

            <p><strong>Mobility:</strong>
                {{ data_get($function->effects, 'Mobility', 0) }}
            </p>

            <a href="{{ route('functions.index') }}">
                <button type="button">Back</button>
            </a>

            @auth
                @if(auth()->user()->role === 'admin')

                    <a href="{{ route('functions.edit', $function->id) }}">
                        <button type="button">Edit</button>
                    </a>

                    <button type="button" class="delete">Delete</button>

                @endif
            @endauth

        </div>

        <div class="image-section">
            <img 
                src="{{ asset($function->image) }}" 
                alt="Image from {{ $function->name }}"
            >
        </div>

    </div>
</body>

</html>