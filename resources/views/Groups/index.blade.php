<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overview - Groups</title>

    <link href="{{ asset('css/overviewFunctionStyle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/overviewGroupStyle.css') }}" type="text/css" rel="stylesheet" />
    <link href="{{ asset('css/navbarStyle.css') }}" type="text/css" rel="stylesheet" />

    <script src="{{ asset('js/groupTable.js') }}" defer></script>
</head>

<body>
    <x-navbar />

    <div class="overviewContent">
        <nav class="overviewTabs" aria-label="Overview navigation">
            <a href="{{ route('functions.index') }}" class="overviewTab">
                Functions
            </a>

            <a
                href="{{ route('groups.index') }}"
                class="overviewTab active"
                aria-current="page"
            >
                Groups
            </a>

            <a href="{{ route('events.index') }}" class="overviewTab">
                Events
            </a>
        </nav>

        <div class="topbar">
            <h1>All groups:</h1>
        </div>

        <div class="button-container">
            <a href="{{ route('groups.add') }}" class="addButton">
            Add Group
        </a>
        </div>

        <div class="category">
            <input
                type="text"
                id="myInput"
                placeholder="Search for names.."
                aria-label="Search groups"
            >
        </div>

        @php
            $arrGroups = $groups->toArray();
            $arrGroupName = array_column($arrGroups, 'name');

            array_multisort($arrGroupName, SORT_ASC, $arrGroups);
        @endphp

        <ul id="functionsList">
            @foreach($arrGroups as $group)
                <li class="functionItem groupItem">
                    <div class="groupSummary">
                        <p class="functionName">
                            {{ $group['name'] }}
                        </p>

                        <p class="functionCategory">
                            {{ !empty($group['is_system']) ? 'System group' : 'Custom group' }}
                        </p>
                    </div>

                    <div class="groupFunctions">
                        @forelse($group['functions'] ?? [] as $function)
                            <span class="functionPill">
                                {{ $function['name'] }}
                            </span>
                        @empty
                            <span class="emptyState">
                                No functions in this group
                            </span>
                        @endforelse
                    </div>

                    <div class="groupButtons">
                        <a
                            href="{{ route('groups.edit', $group['id']) }}"
                            class="editButton"
                        >
                            Edit
                        </a>
                    </div>

                    <div class="groupCount">
                        {{ count($group['functions'] ?? []) }} functions
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</body>

</html>