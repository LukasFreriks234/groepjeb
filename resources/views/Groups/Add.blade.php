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

                <div class="form-group">
                    <label for="function_id">
                        Function
                    </label>

                    <select id="function_id" name="function_id">
                        <option value="">
                            -- Select Function --
                        </option>

                        @foreach($functions as $function)
                            <option value="{{ $function->id }}">
                                {{ $function->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <h2>Group Effects</h2>

                    <div class="effects-grid">

                        <label for="safety">Safety</label>
                        <input
                            type="number"
                            id="safety"
                            min="-10"
                            max="10"
                            name="safety"
                            value="0"
                        >

                        <label for="recreation">Recreation</label>
                        <input
                            type="number"
                            id="recreation"
                            min="-10"
                            max="10"
                            name="recreation"
                            value="0"
                        >

                        <label for="environmental_quality">
                            Environmental Quality
                        </label>
                        <input
                            type="number"
                            id="environmental_quality"
                            min="-10"
                            max="10"
                            name="environmental_quality"
                            value="0"
                        >

                        <label for="services">Services</label>
                        <input
                            type="number"
                            id="services"
                            min="-10"
                            max="10"
                            name="services"
                            value="0"
                        >

                        <label for="mobility">Mobility</label>
                        <input
                            type="number"
                            id="mobility"
                            min="-10"
                            max="10"
                            name="mobility"
                            value="0"
                        >
                    </div>
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