<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $function->name }}</title>
    <link href="{{ asset('css/overviewFunctionStyle.css')}}" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">
</head>

<body>

    <h1>{{ $function->name }}</h1>

    <div class="container">

        <div class="form-section">

            <p><strong>Name:</strong>{{ $function->name }}</p>

            <p><strong>Category:</strong>{{ $function->category }}</p>

            <h2>Effects</h2>

            <p><strong>Safety:</strong>
                {{ $function->effects->Safety }}
            </p>

            <p><strong>Recreation:</strong>
                {{ $function->effects->Recreation }}
            </p>

            <p><strong>Environmental Quality:</strong>
                {{ data_get($function->effects, 'Environmental Quality') }}
            </p>

            <p><strong>Services:</strong>
                {{ $function->effects->Services }}
            </p>

            <p><strong>Mobility:</strong>
                {{ $function->effects->Mobility }}
            </p>

            <a href="/overview"><button>Back</button></a>
            <a href="{{ route('functions.edit', $function->id) }}"><button>Edit</button></a>
            <a><button class="delete">Delete</button></a>

        </div>

        <div class="image-section">
            <img src="{{ asset($function->image) }}" alt="Image from {{ $function->name }}">
        </div>

    </div>
</body>

</html>