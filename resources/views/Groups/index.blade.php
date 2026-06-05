<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups Overview</title>
    <link href="{{ asset('css/group.css') }}" rel="stylesheet">
</head>

<body>
    <div class="group-page">
        <div class="button-container">
            <a href="{{ route('groups.add') }}" class="add-button">
                Add Group
            </a>
        </div>

        @foreach($groups as $group)

            <div class="group-table-container">

                <span>{{ $group->name }}</span>

                <a href="{{ route('groups.edit', $group->id) }}"
                   class="edit-button">
                    Edit
                </a>

            </div>

        @endforeach

    </div>

</body>

</html>