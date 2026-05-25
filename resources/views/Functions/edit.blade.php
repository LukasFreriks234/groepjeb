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

            <div>
                <label for="category">Category</label>
                <select id="category" name="category">
                    @foreach($categories as $category)
                        <option
                            value="{{ $category->category }}"
                            {{ $function->category == $category->category ? 'selected' : '' }}>
                            {{ $category->category }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Add relationship</label>
                <select name="new_category">
                    <option value=""
                        {{ session('relationship_' . $function->id) == '' ? 'selected' : '' }}>
                        -- No relationship --
                    </option>
                    @foreach($categories as $category)
                        <option
                            value="{{ $category->category }}"
                            {{ session('relationship_' . $function->id) == $category->category ? 'selected' : '' }}>
                            {{ $category->category }}
                        </option>
                    @endforeach
                </select>
            </div>
            <h2>Effects</h2>

            <div>
                <label>Safety</label>
                <input
                    type="number"
                    name="Safety"
                    value="{{ $function->effects->Safety }}"
                    class="
                    @if($function->effects->Safety > 0)
                        positiveEffect
                    @elseif($function->effects->Safety < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                    ">
            </div>
            <div>
                <label>Recreation</label>
                <input
                    type="number"
                    name="Recreation"
                    value="{{ $function->effects->Recreation }}"
                    class="
                    @if($function->effects->Recreation > 0)
                        positiveEffect
                    @elseif($function->effects->Recreation < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                    ">
            </div>
            <div>
                <label>Environmental Quality</label>
                <input
                    type="number"
                    name="Environmental_Quality"
                    value="{{ $function->effects->{'Environmental Quality'} }}"
                    class="
                    @if($function->effects->{'Environmental Quality'} > 0)
                        positiveEffect
                    @elseif($function->effects->{'Environmental Quality'} < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                    ">
            </div>
            <div>
                <label>Services</label>
                <input
                    type="number"
                    name="Services"
                    value="{{ $function->effects->Services }}"
                    class="
                    @if($function->effects->Services > 0)
                        positiveEffect
                    @elseif($function->effects->Services < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                    ">
            </div>
            <div>
                <label>Mobility</label>
                <input
                    type="number"
                    name="Mobility"
                    value="{{ $function->effects->Mobility }}"
                    class="
                    @if($function->effects->Mobility > 0)
                        positiveEffect
                    @elseif($function->effects->Mobility < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                    ">
            </div>
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
