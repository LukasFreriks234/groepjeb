<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Function</title>
    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">
</head>

<body>

<h1>Edit Function</h1>

<div class="container">

    <div class="form-section">

        <form method="POST" action="{{ route('functions.update', $function->id) }}">
            @csrf
            @method('PATCH')

            <x-formInput
                name="name"
                label="Name"
                :value="$function->name"
            />

            <label for="category">Category</label>
            <select id="category" name="category">
                @foreach($categories as $category)
                    <option value="{{ $category->category }}"
                        {{ $function->category == $category->category ? 'selected' : '' }}>
                        {{ $category->category }}
                    </option>
                @endforeach
            </select>

            <h2>Effects</h2>

            <x-formInput
                name="Safety"
                label="Safety"
                type="number"
                :value="$function->effects->Safety"
            />

            <x-formInput
                name="Recreation"
                label="Recreation"
                type="number"
                :value="$function->effects->Recreation"
            />

            <x-formInput
                name="Environmental_Quality"
                label="Environmental Quality"
                type="number"
                :value="$function->effects->{'Environmental Quality'}"
            />

            <x-formInput
                name="Services"
                label="Services"
                type="number"
                :value="$function->effects->Services"
            />

            <x-formInput
                name="Mobility"
                label="Mobility"
                type="number"
                :value="$function->effects->Mobility"
            />

            <button type="submit">Save changes</button>

        </form>

    </div>

    <div class="image-section">
        <img src="{{ asset($function->image) }}"
             alt="Afbeelding van {{ $function->name }}">
    </div>

</div>

</body>
</html>
