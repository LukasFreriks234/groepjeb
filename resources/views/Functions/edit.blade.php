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


            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ $function->name }}">
              
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

            <label for="Safety">Safety</label>
            <input id="Safety" type="number" name="Safety"
                   value="{{ $function->effects->Safety }}">

            <label for="Recreation">Recreation</label>
            <input id="Recreation" type="number" name="Recreation"
                   value="{{ $function->effects->Recreation }}">

            <label for="EnvironmentalQuality">Environmental Quality</label>
            <input id="EnvironmentalQuality" type="number"
                   name="Environmental Quality"
                   value="{{ $function->effects->{'Environmental Quality'} }}">

            <label for="Services">Services</label>
            <input id="Services" type="number" name="Services"
                   value="{{ $function->effects->Services }}">

            <label for="Mobility">Mobility</label>
            <input id="Mobility" type="number" name="Mobility"
                   value="{{ $function->effects->Mobility }}">

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