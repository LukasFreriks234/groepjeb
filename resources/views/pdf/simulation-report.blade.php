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

<h2>City Grid</h2>

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

                <td class="grid-cell">

                    @if($cell && $cell->cityFunction)
                        <img
                            src="{{ public_path($cell->cityFunction->image) }}"
                            class="grid-image"
                        >
                    @endif

                    @if($cell && $cell->events && $cell->events->count())
                        <div class="event-container">

                            @foreach($cell->events as $event)

                                <img
                                    src="{{ public_path($event->image_url) }}"
                                    class="event-image"
                                >

                            @endforeach

                        </div>
                    @endif

                </td>

            @endfor

        </tr>

    @endfor

</table>

<h2>Effects</h2>

<table class="effects-table">

    <thead>
        <tr>
            <th>Category</th>
            <th>Score</th>
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