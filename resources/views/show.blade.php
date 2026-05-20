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
            {{ $function->effects->first()?->Veiligheid }}
        </p>

        <p><strong>Recreatie:</strong>
            {{ $function->effects->first()?->Recreatie }}
        </p>

        <p><strong>Milieukwaliteit:</strong>
            {{ $function->effects->first()?->Milieukwaliteit }}
        </p>

        <p><strong>Voorzieningen:</strong>
            {{ $function->effects->first()?->Voorzieningen }}
        </p>

        <p><strong>Mobiliteit:</strong>
            {{ $function->effects->first()?->Mobiliteit }}
        </p>
    </div>
</body>

</html>