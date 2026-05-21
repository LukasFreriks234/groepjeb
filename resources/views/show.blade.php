<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="{{ asset('css/overviewFunctionStyle.css')}}" type="text/css" rel="stylesheet" />
</head>

<body>
    <div>

        <div class="functionImage">
            <img src="{{ asset($function->image) }}">
        </div>
        <p><strong>Name:</strong>{{ $function->name }}</p>
        <p><strong>Category:</strong>{{ $function->category }}</p>
        <p><strong>Safety:</strong>
            {{ $function->effects->Safety }}
        </p>

        <p><strong>Recreation:</strong>
            {{ $function->effects->Recreation }}
        </p>

        <p><strong>Environmental Quality:</strong>
            {{ data_get($function->effects->first(), 'Environmental Quality') }}
        </p>

        <p><strong>Services:</strong>
            {{ $function->effects->Services }}
        </p>

        <p><strong>Mobility:</strong>
            {{ $function->effects->Mobility }}
        </p>
    </div>

    <a href="{{ route('functions.edit', $function->id) }}">
    Edit
</a>
</body>

</html>