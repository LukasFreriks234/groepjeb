<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groups Overview</title>

    <link href="{{ asset('css/navbarStyle.css') }}" rel="stylesheet">
    <link href="{{ asset('css/group.css') }}" rel="stylesheet">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
</head>

<body>

    <x-navbar />

    <div class="group-page">
        <div class="button-container">
            <a href="{{ route('groups.add') }}" class="add-button">
                Add Group
            </a>
        </div>

        <div class="group-table-container">
            <table class="group-table">
                <thead>
                    <tr>
                        <th>Group</th>
                        <th>Functions</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>

                            <td>
                                @forelse($group->functions as $function)
                                    {{ $function->name }}<br>
                                @empty
                                    No functions assigned
                                @endforelse
                            </td>

                            <td>
                                <a href="{{ route('groups.edit', $group->id) }}" class="edit-button">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

        <?php
            $arrGroups = $groups->toArray();
            $arrGroupName = array_column($arrGroups, 'name');
            array_multisort($arrGroupName, SORT_ASC, $arrGroups);
        ?>

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
                        <a href="{{ route('groups.edit', $group['id']) }}"
                           class="editButton">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('myInput');
            const rows = document.querySelectorAll('#functionsList .groupItem');

            function filterGroups() {
                const filter = input.value.trim().toUpperCase();

                rows.forEach(function (row) {

                    const name =
                        row.querySelector('.functionName')
                            .innerText
                            .toUpperCase();

                    const functions =
                        row.querySelector('.groupFunctions')
                            .innerText
                            .toUpperCase();

                    row.style.display =
                        (name.indexOf(filter) > -1 ||
                         functions.indexOf(filter) > -1)
                            ? ''
                            : 'none';
                });
            }

            input.addEventListener('keyup', filterGroups);
            filterGroups();
        });
    </script>
</body>

</html>