<div id="functions_table">
    <input type="text" id="myInput" placeholder="Search for names.."><br>

    {{-- filter doet het niet --}}

    @foreach($categories as $category)
        <input type="checkbox" id="" class="function_filter" name="" value="{{ $category }}">
        <label for="">{{ $category->category }}</label><br>
    @endforeach

    {{-- oude filter --}}
    {{--     
    <input type="checkbox" id="category1" class="function_filter" name="category1" value="Germany">
    <label for="category1"> Germany</label><br>
    <input type="checkbox" id="category2" class="function_filter" name="category2" value="Sweden">
    <label for="category2"> Sweden</label><br>
    <input type="checkbox" id="category3" class="function_filter" name="category3" value="UK">
    <label for="category3"> UK</label><br> --}}


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