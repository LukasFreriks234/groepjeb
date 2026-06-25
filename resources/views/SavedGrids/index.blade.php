<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved grids</title>

    <link href="{{ asset('css/overviewFunctionStyle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/overviewSavedGridStyle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/navbarStyle.css') }}" type="text/css" rel="stylesheet" />
</head>
<body>
    <x-navbar />

    <div class="overviewContent savedGridOverview">
        <div class="topbar">
            <div>
                <h1>Saved grids</h1>
                <p class="overviewSubtitle">Each save is grouped by name and timestamp.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="savedGridStatus">
                {{ session('status') }}
            </div>
        @endif

        @if($savedGrids->isEmpty())
            <div class="emptyState">
                No saved grids yet.
            </div>
        @else
            <table class="savedGridTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Saved at</th>
                        <th>Items</th>
                        <th>Functions</th>
                        <th>Events</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($savedGrids as $savedGrid)
                        <tr>
                            <td>{{ $savedGrid->display_name }}</td>
                            <td>{{ $savedGrid->saved_at }}</td>
                            <td>{{ $savedGrid->item_count }}</td>
                            <td>{{ $savedGrid->function_count }}</td>
                            <td>{{ $savedGrid->event_count }}</td>
                            <td>
                                <form method="POST" action="{{ route('saved-grids.load') }}">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $savedGrid->name }}">
                                    <input type="hidden" name="created_at" value="{{ $savedGrid->created_at }}">
                                    <button type="submit" class="loadButton">Load</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>