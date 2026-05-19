<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Function</title>
</head>
<body>

    <h1>Edit Function</h1>

    <form method="POST"
          action="{{ route('functions.update', $function->id) }}">

        @csrf

        <div>
            <label>Name</label>

            <input type="text"
                   name="name"
                   value="{{ $function->name }}">
        </div>

        <br>

        <div>
            <label>Category</label>

            <input type="text"
                   name="category"
                   value="{{ $function->category }}">
        </div>

        <br>

        <h2>Effects</h2>

        <div>
            <label>Safety</label>

            <input type="number"
                   name="Safety"
                   value="{{ $function->effects->Safety }}">
        </div>

        <br>

        <div>
            <label>Recreation</label>

            <input type="number"
                   name="Recreation"
                   value="{{ $function->effects->Recreation }}">
        </div>

        <br>

        <div>
            <label>Environmental Quality</label>

            <input type="number"
                   name="Environmental Quality"
                   value="{{ $function->effects->{'Environmental Quality'} }}">
        </div>

        <br>

        <div>
            <label>Services</label>

            <input type="number"
                   name="Services"
                   value="{{ $function->effects->Services }}">
        </div>

        <br>

        <div>
            <label>Mobility</label>

            <input type="number"
                   name="Mobility"
                   value="{{ $function->effects->Mobility }}">
        </div>

        <br>

        <button type="submit">
            Opslaan
        </button>

    </form>

</body>
</html>