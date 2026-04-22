<div id="functionsTable">
    <input type="text" id="myInput" placeholder="Search for names.."><br>

    <?php 
    $arrCategories = $categories->toArray();
    $arrCategorie = array_column($arrCategories, 'category');
    array_multisort($arrCategorie, SORT_ASC, $arrCategories);
    $i=1;?>
    @foreach($arrCategories as $category)
        <input type="checkbox" id="category{{ $i }}" class="functionFilter" name="category{{ $i }}" value="{{ $category['category'] }}">
        <label for="category{{ $i }}">{{ $category['category'] }}</label><br>
        <?php $i++;?>
    @endforeach

        <?php 
        $arrFunctions = $functions->toArray();
        $arrFunctionName = array_column($arrFunctions, 'name');
        $arrFunctionCategory = array_column($arrFunctions, 'category');
        array_multisort($arrFunctionCategory, SORT_ASC, $arrFunctionName, SORT_ASC,$arrFunctions);?>
        <ul id="functionsList">
            @foreach($arrFunctions as $function)
            <li id="function{{ $function['id'] }}" class="functionItem" draggable="true">
                <div class="functionImage">
                    <img src="{{ $function['image'] }}">
                </div>
                <div>
                    <p class="functionName">{{ $function['name'] }}</p>
                    <p class="functionCategory" name="{{ $function['category'] }}">{{ $function['category'] }}</p>
                </div>
            </li>
            @endforeach
        </ul>
</div>