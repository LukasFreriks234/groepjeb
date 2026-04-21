<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    @foreach($categories as $category)
        <input type="checkbox" id="category{{ $loop->iteration }}" class="function_filter" name="category{{ $loop->iteration }}" value="{{ $category->category }}">
        <label for="category{{ $loop->iteration }}">{{ $category->category }}</label><br>
    @endforeach

    @foreach($functions as $function)
        <li id="function-4" class="function-item" draggable="true">
            <div class="function_image">
                <img src="{{ $function->image }}">
            </div>
            <div>
                <p class="function_name">{{ $function->name }}</p>
                <p class="function_category" name="{{ $function->category }}">{{ $function->category }}</p>
            </div>
        </li>
    @endforeach
</body>

</html>