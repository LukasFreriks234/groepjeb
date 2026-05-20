<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overview</title>
    <link href="{{ asset('css/overviewFunctionStyle.css')}}" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('js/functionTable.js') }}" defer></script>
</head>

<body>
    <div class="topbar">
        <h1>All functions:</h1>
        <!-- Link for new page -->
        <a href="#"><button class="createButton">Create new function</button></a>
    </div>
    <div class="category">
        <input type="text" id="myInput" placeholder="Search for names.."><br>
        <?php 
        $arrCategories = $categories->toArray();
        $arrCategorie = array_column($arrCategories, 'category');
        array_multisort($arrCategorie, SORT_ASC, $arrCategories);
        $i = 1;?>
        <div class="categoryFilterContainer">
            @foreach($arrCategories as $category)
                <div class="categoryFilter">
                    <input type="checkbox" id="category{{ $i }}" class="functionFilter" name="category{{ $i }}"
                        value="{{ $category['category'] }}">
                    <label for="category{{ $i }}">{{ $category['category'] }}</label><br>
                    <?php    $i++;?>
                </div>
            @endforeach
        </div>
    </div>

    <?php 
        $arrFunctions = $functions->toArray();
$arrFunctionName = array_column($arrFunctions, 'name');
$arrFunctionCategory = array_column($arrFunctions, 'category');
array_multisort($arrFunctionCategory, SORT_ASC, $arrFunctionName, SORT_ASC, $arrFunctions);
    ?>

    <ul id="functionsList">
        @foreach($arrFunctions as $function)
            <a href="/overview/{{ $function['id'] }}">
                <li id="function{{ $function['id'] }}" class="functionItem" draggable="true">
                    <div class="functionNameImage">
                        <div class="functionImage">
                            <img src="{{ $function['image'] }}">
                        </div>
                        <div>
                            <p class="functionName">{{ $function['name'] }}</p>
                            <p class="functionCategory" name="{{ $function['category'] }}">{{ $function['category'] }}</p>
                        </div>
                    </div>
                </li>
            </a>
        @endforeach
    </ul>

    <a href="/"><button class="backButton">Back</button></a>
</body>

</html>