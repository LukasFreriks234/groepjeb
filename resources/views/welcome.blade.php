<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="functions.js" defer></script>
    <link href="{{ asset('css/styles.css')}}" type="text/css" rel="stylesheet"/>
    <title>Document</title>
</head>
<body>
    <div id="functions_table">
        <!--<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names..">

        <table id="myTable">
            <tr class="header">
                <th>Name</th>
                <th>Country</th>
            </tr>
            <tr>
                <td>Alfreds Futterkiste</td>
                <td>Germany</td>
            </tr>
            <tr>
                <td>Berglunds snabbkop</td>
                <td>Sweden</td>
            </tr>
            <tr>
                <td>Island Trading</td>
                <td>UK</td>
            </tr>
            <tr>
                <td>Koniglich Essen</td>
                <td>Germany</td>
            </tr>
        </table>-->

            <ul>
                
                <li><div class="function_image"><img src=
                "{{url('/images/photo-masthead-375-BoK_p8LG.webp')}}"
                ></div>
                <div><p class="function_name">
                Alfreds Futterkiste
                </p><p class="function_category">
                Germany
                </p></div></li>

                <li><div class="function_image"><img src=
                "{{url('/images/UtsiktverJrvsfrnStenegrd_CMSTemplate.jpg')}}"
                ></div>
                <div><p class="function_name">
                Berglunds snabbkop
                </p><p class="function_category">
                Sweden
                </p></div></li>

                <li><div class="function_image"><img src=
                "{{url('/images/LY-AR6.webp')}}"
                ></div>
                <div><p class="function_name">
                Island Trading
                </p><p class="function_category">
                UK
                </p></div></li>

                <li><div class="function_image"><img src=
                "{{url('/images/Essen-Südviertel_Luft.jpg')}}"
                ></div>
                <div><p class="function_name">
                Koniglich Essen
                </p><p class="function_category">
                Germany
                </p></div></li>

            </ul>
    </div>
</body>
</html>
