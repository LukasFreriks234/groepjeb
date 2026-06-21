<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simulation Report</title>

    <style>
        {!! file_get_contents(public_path('css/pdf-report.css')) !!}
    </style>
</head>
<body>

<h1>Simulation Report</h1>

<h2>Grid</h2>

<div class="sr-only">
    <p>
        This grid contains the following city functions:
    </p>

    <ul>
        @foreach($cells as $cell)
            @if($cell->cityFunction)
                <li>
                    Row {{ $cell->y_coordinate + 1 }},
                    Column {{ $cell->x_coordinate + 1 }},
                    {{ $cell->cityFunction->name }}
                </li>
            @endif
        @endforeach
    </ul>
</div>

<table class="grid-table">
    @for($y = 0; $y <= $cells->max('y_coordinate'); $y++)
        <tr>

            @for($x = 0; $x <= $cells->max('x_coordinate'); $x++)

                @php
                    $cell = $cells->first(function ($c) use ($x, $y) {
                        return $c->x_coordinate == $x
                            && $c->y_coordinate == $y;
                    });
                @endphp

                <td>
                    @if($cell && $cell->cityFunction)
                        <img
                            src="{{ public_path($cell->cityFunction->image) }}"
                            alt="Row {{ $y + 1 }} Column {{ $x + 1 }} {{ $cell->cityFunction->name }}"
                            class="grid-image"
                        >
                    @endif
                </td>

            @endfor

        </tr>
    @endfor
</table>

<h2>Effects</h2>

<p class="quality-score">
    Overall Quality of Life score:
    <strong>{{ $qualityOfLife }}</strong>
</p>

<table class="effects-table">
    <thead>
        <tr>
            <th scope="col">Category</th>
            <th scope="col">Effect</th>
        </tr>
    </thead>

    <tbody>

        @foreach($categories as $category)
            <tr>
                <td>{{ $category->category }}</td>
                <td>{{ $effectTotals[$category->category] ?? 0 }}</td>
            </tr>
        @endforeach

        <tr class="quality-row">
            <td>Quality of Life</td>
            <td>{{ $qualityOfLife }}</td>
        </tr>

    </tbody>

</table>

</body>
</html>