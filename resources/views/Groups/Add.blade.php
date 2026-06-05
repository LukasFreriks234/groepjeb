<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Group</title>

    <link href="{{ asset('css/group.css') }}" type="text/css" rel="stylesheet" />
</head>

<body>

    <main class="group-page">

        <h1>Add Group</h1>

        <div class="group-form-container">

            <form method="POST" action="{{ route('groups.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">
                        Group Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                    >
                </div>

                <div class="button-group">

                    <button
                        type="submit"
                        class="save-button"
                    >
                        Add Group
                    </button>

                    <a
                        href="{{ route('groups.index') }}"
                        class="back-button"
                    >
                        Back
                    </a>

                </div>

            </form>

        </div>

    </main>

</body>

</html>