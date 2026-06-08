<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups Overview</title>

    <link href="{{ asset('css/navbarStyle.css') }}" rel="stylesheet">
    <link href="{{ asset('css/group.css') }}" rel="stylesheet">
</head>

<body>

    <x-navbar />

    <div class="group-page">
        <div class="button-container">
            <a href="{{ route('groups.add') }}" class="add-button">
                Add Group
            </a>
        </div>

        <div class="group-table-container">
            <table class="group-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Functions</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>

                            <td>
                                @forelse($group->functions as $function)
                                    {{ $function->name }}<br>
                                @empty
                                    No functions assigned
                                @endforelse
                            </td>

                            <td>
                                <a href="{{ route('groups.edit', $group->id) }}" class="edit-button">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>