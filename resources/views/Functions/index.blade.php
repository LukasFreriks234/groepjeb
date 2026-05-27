<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overview</title>

    <link href="{{ asset('css/overviewFunctionStyle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/navbarStyle.css') }}" type="text/css" rel="stylesheet" />

    <script type="text/javascript" src="{{ asset('js/functionTable.js') }}" defer></script>
</head>

<body>
    <x-navbar />

    <div class="overviewContent">
        <div class="topbar">
            <h1>All functions:</h1>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('functions.create') }}">
                        <button class="createButton">Create function</button>
                    </a>
                @endif
            @endauth
        </div>

        <div class="category">
            <input type="text" id="myInput" placeholder="Search for names.."><br>

            <?php 
                $arrCategories = $categories->toArray();
                $arrCategorie = array_column($arrCategories, 'category');
                array_multisort($arrCategorie, SORT_ASC, $arrCategories);
                $i = 1;
            ?>

            <div class="categoryFilterContainer">
                @foreach($arrCategories as $category)
                    <div class="categoryFilter">
                        <input 
                            type="checkbox" 
                            id="category{{ $i }}" 
                            class="functionFilter" 
                            name="category{{ $i }}"
                            value="{{ $category['category'] }}"
                        >
                        <label for="category{{ $i }}">{{ $category['category'] }}</label><br>
                        <?php $i++; ?>
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
                <a href="{{ route('functions.show', $function['id']) }}" class="noStyle">
                    <li 
                        id="function{{ $function['id'] }}" 
                        class="functionItem" 
                        draggable="true"
                        data-function-id="{{ $function['id'] }}"
                        data-category="{{ $function['category'] }}"
                    >
                        <div class="functionNameImage">
                            <div class="functionImage">
                                <img 
                                    src="{{ asset($function['image']) }}" 
                                    alt="{{ $function['name'] }}"
                                >
                            </div>

                            <div>
                                <p class="functionName">{{ $function['name'] }}</p>
                                <p class="functionCategory" name="{{ $function['category'] }}">
                                    {{ $function['category'] }}
                                </p>
                            </div>
                        </div>
                    </li>
                </a>
            @endforeach
        </ul>
    </div>
</body>

</html>