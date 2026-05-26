<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Function</title>
    <link rel="stylesheet" href="{{ asset('css/editStyle.css') }}">
</head>

<body>
    <h1>Create Function</h1>

    <div class="container">
        <div class="form-section">
            <form method="POST" action="{{ route('functions.store') }}" enctype="multipart/form-data">
                @csrf

                <x-formInput
                    name="name"
                    label="Name"
                    :value="old('name')"
                />

                <label for="image">Image</label>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/*"
                    alt="Upload an image for the new function"
                >

                <label for="category">Category</label>
                <select id="category" name="category">
                    @foreach($categories as $category)
                        <option value="{{ $category->category }}"
                            {{ old('category') == $category->category ? 'selected' : '' }}>
                            {{ $category->category }}
                        </option>
                    @endforeach
                </select>

                <h2>Effects</h2>

                <x-formInput
                    name="Safety"
                    label="Safety"
                    type="number"
                    :value="old('Safety', 0)"
                    min="-10"
                    max="10"
                />

                <x-formInput
                    name="Recreation"
                    label="Recreation"
                    type="number"
                    :value="old('Recreation', 0)"
                    min="-10"
                    max="10"
                />

                <x-formInput
                    name="Environmental_Quality"
                    label="Environmental Quality"
                    type="number"
                    :value="old('Environmental_Quality', 0)"
                    min="-10"
                    max="10"
                />

                <x-formInput
                    name="Services"
                    label="Services"
                    type="number"
                    :value="old('Services', 0)"
                    min="-10"
                    max="10"
                />

                <x-formInput
                    name="Mobility"
                    label="Mobility"
                    type="number"
                    :value="old('Mobility', 0)"
                    min="-10"
                    max="10"
                />

                <button type="submit">Create function</button>
            </form>
        </div>
    </div>
</body>

</html>