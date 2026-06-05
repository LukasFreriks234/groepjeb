<link rel="stylesheet" href="{{ asset('css/group.css') }}">

<h1>Add Group</h1>

<div class="group-form-container">
    <form method="POST" action="{{ route('groups.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Group Name</label>

            <input
                type="text"
                id="name"
                name="name"
                required
            >
        </div>

        <div class="button-group">
            <button type="submit" class="save-button">
                Add Group
            </button>

            <a href="{{ route('groups.index') }}" class="back-button">
                Back
            </a>
        </div>
    </form>
</div>