<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Function</title>

    <link rel="stylesheet"
          href="{{ asset('css/editStyle.css') }}">
</head>

<body>

<h1>Edit Function</h1>
<div class="container">

    <div class="form-section">
        <form method="POST"
              action="{{ route('functions.update', $function->id) }}">
            @csrf
            @method('PATCH')

            <!-- NAME -->
            <x-formInput
                name="name"
                label="Name"
                :value="$function->name"
            />

            <!-- CATEGORY -->
            <label for="category">
                Category
            </label>
            <select id="category"
                    name="category">
                @foreach($categories as $category)
                    <option
                        value="{{ $category->category }}"

                        @if(
                            $function->category
                            == $category->category
                        )
                            selected
                        @endif>
                        {{ $category->category }}
                    </option>
                @endforeach
            </select>

            <!-- ADD RELATIONSHIP -->
            <h2>Add Relationship</h2>
            <label>
                Select Function
            </label>
            <select name="related_function">
                <option value="">
                    -- Select Function --
                </option>
                @foreach($functions as $relatedFunction)
                    @if(
                        $relatedFunction->id
                        != $function->id
                    )
                        <option
                            value="{{ $relatedFunction->id }}"
                            @if(
                                $function->related_function_id
                                == $relatedFunction->id
                            )
                                selected
                            @endif>
                            {{ $relatedFunction->name }}
                        </option>
                    @endif
                @endforeach
            </select>

            <!-- RELATIONSHIP EFFECTS -->
            <h2>Relationship Effects</h2>
            <label>Safety</label>
            <input
                type="number"
                name="relationship_safety"
                min="-10"
                max="10"
                value="{{ $function->relationship_safety ?? 0 }}"
                class="
                    @if(($function->relationship_safety ?? 0) > 0)
                        positiveEffect
                    @elseif(($function->relationship_safety ?? 0) < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                ">


            <label>Recreation</label>
            <input
                type="number"
                name="relationship_recreation"
                min="-10"
                max="10"
                value="{{ $function->relationship_recreation ?? 0 }}"
                class="
                    @if(($function->relationship_recreation ?? 0) > 0)
                        positiveEffect
                    @elseif(($function->relationship_recreation ?? 0) < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                ">

            <label>Environmental Quality</label>
            <input
                type="number"
                name="relationship_environmental"
                min="-10"
                max="10"
                value="{{ $function->relationship_environmental ?? 0 }}"
                class="
                    @if(($function->relationship_environmental ?? 0) > 0)
                        positiveEffect
                    @elseif(($function->relationship_environmental ?? 0) < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                ">

            <label>Services</label>
            <input
                type="number"
                name="relationship_services"
                min="-10"
                max="10"
                value="{{ $function->relationship_services ?? 0 }}"
                class="
                    @if(($function->relationship_services ?? 0) > 0)
                        positiveEffect
                    @elseif(($function->relationship_services ?? 0) < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                ">

            <label>Mobility</label>
            <input
                type="number"
                name="relationship_mobility"
                min="-10"
                max="10"
                value="{{ $function->relationship_mobility ?? 0 }}"
                class="
                    @if(($function->relationship_mobility ?? 0) > 0)
                        positiveEffect
                    @elseif(($function->relationship_mobility ?? 0) < 0)
                        negativeEffect
                    @else
                        neutralEffect
                    @endif
                ">

            <!-- EFFECTS -->
            <h2>Effects</h2>
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

            <!-- SAVE -->
            <button type="submit">
                Save changes
            </button>
        </form>
    </div>

    <!-- IMAGE -->
    <div class="image-section">
        <img src="{{ asset($function->image) }}"
             alt="Afbeelding van {{ $function->name }}">
    </div>

</div>

</body>
</html>