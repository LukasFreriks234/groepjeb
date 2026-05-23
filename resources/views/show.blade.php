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
        <p><strong>Veiligheid:</strong>
            {{ $function->effects->Veiligheid }}
        </p>

        <p><strong>Recreatie:</strong>
            {{ $function->effects->Recreatie }}
        </p>

        <p><strong>Milieukwaliteit:</strong>
            {{ $function->effects->Milieukwaliteit }}
        </p>

        <p><strong>Voorzieningen:</strong>
            {{ $function->effects->Voorzieningen }}
        </p>

        <p><strong>Mobiliteit:</strong>
            {{ $function->effects->Mobiliteit }}
        </p>
    </div>

    <a href="{{ route('functions.edit', $function->id) }}">
    Edit
</a>
</body>

</html>