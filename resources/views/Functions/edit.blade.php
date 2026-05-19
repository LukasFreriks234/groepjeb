<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
</head>
<body>

@foreach($functions as $function)

<form method="POST"
      action="{{ route('functions.update', $function->id) }}">

    @csrf

    <div class="card">

        <img src="{{ $function->image }}" width="100">

        <div>
            <label>Name</label>

            <input type="text"
                   name="name"
                   value="{{ $function->name }}">
        </div>

        <div>
            <label>Category</label>

            <input type="text"
                   name="category"
                   value="{{ $function->category }}">
        </div>

        <hr>

        <div>
            <label>Veiligheid</label>

            <input type="number"
                   name="Veiligheid"
                   value="{{ $function->effects->Veiligheid ?? 0 }}">
        </div>

        <div>
            <label>Recreatie</label>

            <input type="number"
                   name="Recreatie"
                   value="{{ $function->effects->Recreatie ?? 0 }}">
        </div>

        <div>
            <label>Milieukwaliteit</label>

            <input type="number"
                   name="Milieukwaliteit"
                   value="{{ $function->effects->Milieukwaliteit ?? 0 }}">
        </div>

        <div>
            <label>Voorzieningen</label>

            <input type="number"
                   name="Voorzieningen"
                   value="{{ $function->effects->Voorzieningen ?? 0 }}">
        </div>

        <div>
            <label>Mobiliteit</label>

            <input type="number"
                   name="Mobiliteit"
                   value="{{ $function->effects->Mobiliteit ?? 0 }}">
        </div>

        <button type="submit">
            Opslaan
        </button>

    </div>

</form>

<hr>

@endforeach

</body>
</html>