<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overview - Functions</title>

    <link href="{{ asset('css/overviewFunctionStyle.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('css/navbarStyle.css') }}" type="text/css" rel="stylesheet">

    <script src="{{ asset('js/functionTable.js') }}" defer></script>
</head>

<body>
    <x-navbar />

    <div class="overviewContent">
        <nav class="overviewTabs" aria-label="Overview navigation">
            <a href="{{ route('functions.index') }}" class="overviewTab active" aria-current="page">
                Functions
            </a>

            <a href="{{ route('groups.index') }}" class="overviewTab">
                Groups
            </a>

            <a href="{{ route('events.index') }}" class="overviewTab">
                Events
            </a>
        </nav>

        <div class="topbar">
            <h1>All functions:</h1>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('functions.create') }}" class="createButton createFunctionLink">
                        Create function
                    </a>
                @endif
            @endauth
        </div>

        <h2>Categories</h2>

        <div class="category">
            <input
                type="text"
                id="myInput"
                placeholder="Search for names.."
                aria-label="Search functions"
            ><br>

            @php
                $arrCategories = $categories->toArray();
                $arrCategorie = array_column($arrCategories, 'category');
                array_multisort($arrCategorie, SORT_ASC, $arrCategories);
                $i = 1;
            @endphp

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

                        <label for="category{{ $i }}">
                            {{ $category['category'] }}
                        </label><br>

                        @php
                            $i++;
                        @endphp
                    </div>
                @endforeach
            </div>
        </div>

        @php
            $arrFunctions = $functions->toArray();
            $arrFunctionName = array_column($arrFunctions, 'name');
            $arrFunctionCategory = array_column($arrFunctions, 'category');

            array_multisort(
                $arrFunctionCategory,
                SORT_ASC,
                $arrFunctionName,
                SORT_ASC,
                $arrFunctions
            );
        @endphp

        <h2>Functions</h2>

        <ul id="functionsList">
            @foreach($arrFunctions as $function)
                <li
                    id="function{{ $function['id'] }}"
                    class="functionItem"
                    draggable="true"
                    data-function-id="{{ $function['id'] }}"
                    data-category="{{ $function['category'] }}"
                >
                    <a
                        href="{{ route('functions.show', $function['id']) }}"
                        class="noStyle"
                        aria-label="Function {{ $function['name'] }} in category {{ $function['category'] }}"
                    >
                        <div class="functionNameImage">
                            <div class="functionImage">
                                <img src="{{ asset($function['image']) }}" alt="">
                            </div>

                            <div>
                                <p class="functionName">
                                    {{ $function['name'] }}
                                </p>

                                <p class="functionCategory">
                                    {{ $function['category'] }}
                                </p>
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</body>

</html>