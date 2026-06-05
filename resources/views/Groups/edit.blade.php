<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Group</title>
    <link href="{{ asset('css/group.css') }}" rel="stylesheet">
</head>
<body>

    <div class="group-page">
        <h1>Edit Group</h1>
        <div class="group-form-container">
            <form method="POST" action="{{ route('groups.update', $group->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">
                        Group Name
                    </label>

                    <input type="text" id="name" name="name" value="{{ $group->name }}" required>
                </div>

                <div class="button-group">
                    <button type="submit" class="save-button">
                        Save Changes
                    </button>

                    <a href="{{ route('groups.index') }}" class="back-button">
                        Back
                    </a>
                </div>
            </form>
        </div>

    </div>
</body>
</html>