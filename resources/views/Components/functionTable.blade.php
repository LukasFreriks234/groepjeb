<div id="functions_table">
    <input type="text" id="myInput" placeholder="Search for names.."><br>

    <?php $i=1;?>
    @foreach($categories as $category)
        <input type="checkbox" id="category{{ $i }}" class="function_filter" name="category{{ $i }}" value="{{ $category->category }}">
        <label for="category{{ $i }}">{{ $category->category }}</label><br>
        <?php $i++;?>
    @endforeach

        <ul id="functions_list">
            @foreach($functions as $function)
                <li id="{{ $function->id }}" class="function-item" draggable="true">
                <div class="function_image">
                    <img src="{{ $function->image }}">
                </div>
                <div>
                    <p class="function_name">{{ $function->name }}</p>
                    <p class="function_category" name="{{ $function->category }}">{{ $function->category }}</p>
                </div>
            </li>
            @endforeach
        </ul>
</div>