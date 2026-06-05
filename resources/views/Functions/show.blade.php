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

            <h2>Function information</h2>

            <p tabindex="0"><strong>Name:</strong> {{ $function->name }}</p>

            <p tabindex="0"><strong>Category:</strong> {{ $function->category }}</p>

            <h2>Effects</h2>

            <p tabindex="0"><strong>Safety:</strong>
                {{ data_get($function->effects, 'Safety', 0) }}
            </p>

            <p tabindex="0"><strong>Recreation:</strong>
                {{ data_get($function->effects, 'Recreation', 0) }}
            </p>

            <p tabindex="0"><strong>Environmental Quality:</strong>
                {{ data_get($function->effects, 'Environmental Quality', 0) }}
            </p>

            <p tabindex="0"><strong>Services:</strong>
                {{ data_get($function->effects, 'Services', 0) }}
            </p>

            <p tabindex="0"><strong>Mobility:</strong>
                {{ data_get($function->effects, 'Mobility', 0) }}
            </p>

            <h2>Relationship</h2>

            @if($function->related_function_id)
                <p tabindex="0"><strong>Related function:</strong>
                    {{ $function->relatedFunction->name ?? 'Unknown function' }}
                </p>

                <p tabindex="0"><strong>Safety:</strong>
                    {{ $function->relationship_safety ?? 0 }}
                </p>

                <p tabindex="0"><strong>Recreation:</strong>
                    {{ $function->relationship_recreation ?? 0 }}
                </p>

                <p tabindex="0"><strong>Environmental Quality:</strong>
                    {{ $function->relationship_environmental ?? 0 }}
                </p>

                <p tabindex="0"><strong>Services:</strong>
                    {{ $function->relationship_services ?? 0 }}
                </p>

                <p tabindex="0"><strong>Mobility:</strong>
                    {{ $function->relationship_mobility ?? 0 }}
                </p>
            @else
                <p tabindex="0">No relationship</p>
            @endif

            <a href="{{ route('functions.index') }}">
                <button type="button" tabindex="-1">Back</button>
            </a>


                    <a href="{{ route('functions.edit', $function->id) }}">
                        <button type="button" tabindex="-1">Edit</button>
                    </a>

                    @auth
                        @if(auth()->user()->role === 'admin')

                    <form action="{{ route('functions.destroy', $function->id) }}" method="POST">
                        @csrf
                        @method('delete')

                        <button type="submit" class="delete">Delete</button>
                    </form>
                @endif
            @endauth

        </div>

        <div class="image-section">
            <img src="{{ asset($function->image) }}" alt="Illustration of {{ $function->name }}">
        </div>

    </div>
</body>

</html>