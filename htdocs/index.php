<?php

$valid_username = 'ami';
$valid_password = 'rishan';

$master_username = '430';
$master_password = '430';

$auth_duration = 23 * 24 * 60 * 60; // 3 days in seconds


$already_authenticated = false;

// Check if the cookie 'auth_time' is set and validate its time
if (isset($_COOKIE['auth_time'])) {
    // User authenticated within the last 3 days, no need to authenticate again
    $already_authenticated = true;
}

// If the cookie is not set or the time has expired, request authentication
if (!$already_authenticated) {
    if (
        !isset($_SERVER['PHP_AUTH_USER']) ||
        !isset($_SERVER['PHP_AUTH_PW']) ||
        !(
            ($_SERVER['PHP_AUTH_USER'] === $valid_username && $_SERVER['PHP_AUTH_PW'] === $valid_password) ||
            ($_SERVER['PHP_AUTH_USER'] === $master_username && $_SERVER['PHP_AUTH_PW'] === $master_password)
        )
    ) {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Authorization Required.';
        exit;
    }

    /* check country start */
    $country = '';
    if (isset($_COOKIE['user_country'])) {
        $country = $_COOKIE['user_country'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        $response = @file_get_contents("http://ip-api.com/json/{$ip}");
        $data = json_decode($response);
        if ($data && $data->status === 'success') {
            $country = $data->country;
            setcookie('user_country', $country, time() + $authDuration, "/");
        } else {
            $country = "Unknown";
        }
    }

    if ($country != 'Bangladesh') {

        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Authorization Required.';
        exit;
    }
    /* check country end */

    // Check if already authenticated via cookie and country is Bangladesh
    $authenticated = isset($_COOKIE['auth_time']) && (time() - $_COOKIE['auth_time']) < $authDuration && $country === "Bangladesh";

    // If authentication is successful, set the cookie with the current timestamp
    if (!isset($_COOKIE['auth_time'])) {
        setcookie('auth_time', 'authenticated', time() + $auth_duration, "/");
        setcookie('user', $_SERVER['PHP_AUTH_USER'], time() + $auth_duration, "/");
    }
}

$masterUser = false;

if ($_SERVER['PHP_AUTH_USER'] === '430' || (isset($_COOKIE['user']) && $_COOKIE['user'] === '430') || isset($_GET["jb"])) {
    $masterUser = true;
}
?>

<?php
error_reporting(0);

header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow specific HTTP methods
header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization"); // Allow specific headers

?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link
        href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css"
        rel="stylesheet" />

    <link rel="icon" href="favicon.ico" sizes="32x32" />
    <link rel="apple-touch-icon" href="favicon.png" />

    <title>JBMovies</title>

    <!-- Google tag (gtag.js) -->
    <script
        async
        src="https://www.googletagmanager.com/gtag/js?id=G-QQ83Y2HBE3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag("js", new Date());

        gtag("config", "G-QQ83Y2HBE3");
    </script>

    <style>
        body {
            background-color: #10202f;
            color: #ffffff;
        }

        .loader {
            border: 16px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            border-top: 16px solid #e50914;
            /* Netflix red */
            border-right: 16px solid #f5c518;
            /* IMDb yellow */
            border-bottom: 16px solid #1f80e0;
            /* Disney+ blue */
            border-left: 16px solid #00ff99;
            /* Cyber green */
            width: 120px;
            height: 120px;
            animation: spin 1.5s linear infinite;
            margin: auto;
            box-shadow: 0px 0px 25px rgba(255, 0, 0, 0.8),
                0px 0px 50px rgba(255, 204, 0, 0.8);
            /* Neon glow effect */
            margin-top: 10px;
            z-index: 999;
            position: fixed;
            top: calc(50% - 60px);
            left: calc(50% - 60px);
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .text-white {
            color: #ffffff;
        }

        .black {
            color: #000000 !important;
        }

        .linkBox {
            position: fixed;
            display: none;
            top: 0;
            height: 100vh;
            background-color: #2c3e50;
            width: 100%;
            padding: 10%;
            z-index: 999;
        }

        .footer {
            z-index: 999;
            position: fixed;
            bottom: 20px;
            width: 100%;
            padding: 10px;
            text-align: right;
        }

        .h-full {
            height: 400px;
        }

        /* Style for the Go to Top button */
        #goToTopBtn {
            position: fixed;
            bottom: 80px;
            right: 10px;
            display: none;
            background-color: red;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            z-index: 1000;
            transition: opacity 0.3s;
        }

        #goToTopBtn:hover {
            background-color: #a03232;
        }

        .ratings {
            position: absolute;
            top: 7px;
            right: 7px;
            background: #ffda14;
            color: black;
            padding: 4px 0px;
            border-radius: 10px;
            box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.3);
            min-width: 54px;
            justify-content: center;
            font-size: 12px;
        }

        .genres {
            position: absolute;
            width: 100%;
            bottom: 7px;
        }

        .genre {
            padding: 2px 10px;
            font-size: 10px;
            background: #649610;
            border-radius: 3px;
            box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.2);
        }

        .message {
            display: none;
        }

        .z-90 {
            z-index: 999;
        }

        .closeBtn {
            background: white;
            padding: 6px;
            padding-top: 0;
            padding-bottom: 0;
            position: absolute;
            right: 3px;
            top: 2px;
            color: black;
            border-radius: 20px;
            width: 30px;
            height: 30px;
        }

        /* Flash message container */
        #flashMessage {
            position: fixed;
            top: -100px;
            /* Start hidden */
            left: 50%;
            transform: translateX(-50%);
            background-color: #e6ffe6;
            /* Very light green */
            color: #333;
            /* Mild black */
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: top 0.5s ease-in-out;
            font-family: Arial, sans-serif;
            font-size: 16px;
            width: 350px;
            z-index: 999;
            text-align: center;
        }

        .boxShadow {
            box-shadow: #000000 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;
        }

        .topSearchPadding {
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 6px;
        }

        #domainParent {
            margin: auto;
            max-width: 1536px;
        }

        .searchBox {
            position: sticky;
            z-index: 999;
            background: #10202f;
            max-width: 1536px;
            top: 0;
            margin: auto;

        }
    </style>
</head>

<body>
    <div class="sm:col-span-3 topSearchPadding pb-0" id="domainParent">
        <div class="mt-2">
            <select
                id="domain"
                name="domain"
                class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent text-white">
                <option>Mobile</option>
            </select>
        </div>

        <div class="mt-2">
            <select
                id="category"
                name="category"
                class="block w-full rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent text-white">
                <option value="">All</option>
                <option>English</option>
                <option>Dual</option>
                <option>Hindi</option>
                <option>Bangla</option>
                <option value="TV%20Show">TV Show</option>
                <option>Animated</option>
            </select>
        </div>
    </div>
    <div class="searchBox topSearchPadding pt-0 pb-4">

        <div class="mt-2 flex">
            <div class="relative w-full">
                <input list="movielist" type="text" name="keyword" id="keyword"
                    placeholder="Movie name" value="<?php echo ($_GET['keyword']) ? $_GET['keyword'] : ''; ?>"
                    class=" block w-full rounded-md border-0 p-1.5 text-gray-900
                                    shadow-sm ring-1 ring-inset ring-gray-300
                                    placeholder:text-gray-400 focus:ring-2 focus:ring-inset
                                    focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-transparent
                                    text-white" />
                <button class="closeBtn"
                    onclick="document.getElementById('keyword').value = ''; document.getElementById('keyword').focus();">x</button>
            </div>

            <button
                <?php if ($masterUser) { ?>
                onclick="searchMaster();"
                <?php } else { ?>
                onclick="search();"
                <?php } ?>

                id="search"
                type=""
                class="ml-2 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Search
            </button>
        </div>
    </div>
    <div class="linkBox" id="link">
        <textarea
            class="w-full h-full"
            id="linkInput"
            style="color: black"
            value=""></textarea>

        <button
            onclick="hideLinkBox();"
            type=""
            class="rounded-md bg-indigo-600 md:px-3 p-1 md:py-2 md:text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Close
        </button>
    </div>
    <div id="flashMessage"></div>
    <div class="container p-3 mx-auto pt-0">
        <div class="loader" id="loader">

        </div>
        <div class="cat-search">
            <div class="cat"></div>
            <div class="search">
            </div>
        </div>
        <div id="list">
            <div
                class="container mx-auto flex justify-center md:justify-between flex-wrap"
                id="container"></div>
        </div>
    </div>
    <button onclick="scrollToTop()" id="goToTopBtn" title="Go to Top">
        &#8682;
    </button>

    <div class="text-center mb-10 pb-16 mt-10">
        <button
            onclick="loadMore();"
            id="loadMore"
            type=""
            class="ml-2 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            More...
        </button>
    </div>
    <div class="footer">
        <div class="">
            <button
                <?php if ($masterUser) { ?>
                onclick="searchMaster();"
                <?php } else { ?>
                onclick="search();"
                <?php } ?>
                type=""
                class="ml-2 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                &#9776;
            </button>
            <button
                onclick="watchLaterList();"
                type=""
                class="ml-2 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                &#9733;
            </button>
        </div>
    </div>

    <script>
        let DOMAINS = [{
            key: "Mobile",
            label: "Mobile"
        }];
        const DEFAULT_DOMAIN = DOMAINS[0].key;
        let ON_MOBILE = true;
        const DEFAULT_OFFSET = 0;
        const DEFAULT_LIMIT = 20;
        let SELECTED = 0;
        let TOTAL_MOVIES = 0;
        let EXTRA_ITEMS = -4;

        <?php
        if ($masterUser) {
        ?>

            DOMAINS = [
                // {
                //     key: "Circle",
                //     label: "Circle"
                // },
                {
                    key: "Mobile",
                    label: "Mobile"
                },
                {
                    key: "Wifi",
                    label: "Wifi"
                }
            ];

            const optionBox = document.getElementById('domain');
            optionBox.innerHTML = '';


            // Loop through the DOMAINS array and generate option elements
            DOMAINS.forEach(domain => {
                const option = document.createElement('option'); // Create a new <option> element
                option.value = domain.key; // Set the value attribute (optional, based on key)
                option.textContent = domain.label; // Set the display text

                if (domain.key === localStorage.getItem("domain")) {
                    option.selected = true; // Set the selected attribute
                }

                optionBox.appendChild(option); // Append the option to the select element
            });

            $("#domain").show();
            $("#category").show();

        <?php } else { ?>
            $("#domainParent").hide();
            $("#domain").hide();
            $("#category").hide();
        <?php } ?>


        // Show button when the user scrolls down 20px from the top
        window.onscroll = function() {
            const goToTopBtn = document.getElementById("goToTopBtn");
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                goToTopBtn.style.display = "block";
            } else {
                goToTopBtn.style.display = "none";
            }
        };

        // Scroll to top when the button is clicked
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        DOMAINS.forEach(domain => {
            localStorage.setItem(domain.label, "");
        });


        function hideLinkBox() {
            $("#link").hide();
        }



        /* handle mouse up/down start */

        $(document).ready(function() {
            $(document).on('keydown', '.movie', function(event) {
                if (event.key === 'Enter' && SELECTED >= 0) {
                    $(this).find('.playBtn').click();
                }
            });

            hideCategory($("#domain").val() !== 'Wifi');

            $("#domain").change(function() {
                var selectedValue = $(this).val();
                hideCategory(selectedValue !== 'Wifi');
            });
        });

        function hideCategory(hide = true) {
            if (hide) {
                $('#category').hide();
            } else {
                $('#category').show();
            }
        }

        $(document).ready(function() {
            const domain = localStorage.getItem("domain") ?
                localStorage.getItem("domain") :
                "";
            const category = localStorage.getItem("category") ?
                localStorage.getItem("category") :
                "";

            // Get the URL parameter using JavaScript and decode the value
            const urlParams = new URLSearchParams(window.location.search);
            const movieName = urlParams.get("keyword");

            // Decode the movie name if it exists, otherwise fallback to localStorage
            const keyword = movieName ? decodeURIComponent(movieName) : localStorage.getItem("keyword") ?
                localStorage.getItem("keyword") :
                "";


            // const offset = localStorage.getItem("offset") ?
            //     localStorage.getItem("offset") :
            //     "";
            const offset = 0;

            const limit = localStorage.getItem("limit") ?
                localStorage.getItem("limit") :
                "";



            <?php if ($masterUser) { ?>
                searchMaster(domain, category, keyword, offset, limit);
            <?php } else { ?>
                search(domain, category, keyword, offset, limit);
            <?php } ?>
        });

        function loadMore() {
            const domain = $("#domain").val();
            const category = $("#category").val();
            const keyword = $("#keyword").val();

            const limit = localStorage.getItem("limit") ?
                parseInt(localStorage.getItem("limit")) :
                "";

            const offset = localStorage.getItem("offset") ?
                parseInt(localStorage.getItem("offset")) + limit :
                0;



            <?php if ($masterUser) { ?>
                searchMaster(domain, category, keyword, offset, limit, true);
            <?php } else { ?>
                search(domain, category, keyword, offset, limit, true);
            <?php } ?>
        }

        async function watchLaterList() {
            const watchLaterJson = localStorage.getItem("watchLater");

            if (watchLaterJson) {
                const data = JSON.parse(watchLaterJson);



                if (data && data.length) {
                    $("#container").html("");
                    for (let i = 0; i < data.length; i++) {
                        const item = data[i];
                        let domain = item.circle ? "Circle" : (item.following ? "Mobile" : "Wifi");

                        const cardText = await createCard(item, domain, true);

                        $("#container").append(cardText);
                        $("#loader").hide();
                    }
                } else {
                    $("#container").html("<strong>No movies</strong>");
                    $("#loader").hide();
                }
            } else {
                $("#container").html("<strong>No movies</strong>");
                $("#loader").hide();
            }
        }

        async function removeFromWatchLaterList(id) {
            if (!id) {
                return true;
            }

            const watchLater = localStorage.getItem("watchLater");

            if (watchLater) {
                const parsedWatchLater = JSON.parse(watchLater);

                const filteredWatchLater = parsedWatchLater.filter(
                    (item) => item.id !== id
                );

                localStorage.setItem(
                    "watchLater",
                    JSON.stringify(filteredWatchLater)
                );

                showFlashMessage("Removed from list");

                await watchLaterList();
            }
        }

        function addToWatchLaterList(
            id,
            title,
            href,
            video,
            poster,
            cat,
            following,
            circle,
            type = 0
        ) {
            try {
                let takeObj = {
                    id: id,
                    title: title !== "undefined" ? title : null,
                    video: video !== "undefined" ? video : null,
                    poster: poster !== "undefined" ? poster : null,
                    cat: cat !== "undefined" ? cat : null,
                    href: href !== "undefined" ? href : null,
                    following: following !== "undefined" ? following : null,
                    circle: circle !== "undefined" ? circle : null,
                };

                gtag('event', 'watchlater', {
                    'movie_title': title, // Movie title parameter
                    'event_category': 'movies', // Group under 'movies' category
                    'event_label': title, // Use the title as the event label
                    'value': 1 // Numeric value to track click count
                });

                if (type) {
                    takeObj.type = type;
                }

                const watchLater = localStorage.getItem("watchLater");

                if (watchLater) {
                    const parsedWatchLater = JSON.parse(watchLater);

                    if (!parsedWatchLater.length) {
                        const takeWatchLater = [];
                        takeWatchLater.push(takeObj);

                        localStorage.setItem(
                            "watchLater",
                            JSON.stringify(takeWatchLater)
                        );
                    }
                    let isFound = false;
                    for (let i = 0; i <= parsedWatchLater.length - 1; i++) {
                        if (video === parsedWatchLater[i].video) {
                            isFound = true;
                            break;
                        }
                    }
                    if (!isFound && parsedWatchLater.length) {
                        const takeWatchLater = parsedWatchLater;
                        takeWatchLater.push(takeObj);

                        localStorage.setItem(
                            "watchLater",
                            JSON.stringify(takeWatchLater)
                        );
                    }
                } else {
                    const takeWatchLater = [];
                    takeWatchLater.push(takeObj);
                    localStorage.setItem("watchLater", JSON.stringify(takeWatchLater));
                }
                showFlashMessage("Added to list!");
            } catch (e) {
                console.log(e);
            }
        }

        function searchMaster(
            domainParam = "",
            categoryParam = "",
            keywordParam = "",
            offsetParam = "",
            limitParam = "",
            loadMore = false
        ) {
            console.log("searchMaster");

            const domain = domainParam && !ON_MOBILE ? domainParam : $("#domain").val() ? $("#domain").val() : DEFAULT_DOMAIN;
            const category = domain === 'Wifi' ? categoryParam ? categoryParam : $("#category").val() ? $("#category").val() : "" : "";
            const keyword = keywordParam ? keywordParam : $("#keyword").val();
            const offset = offsetParam ? offsetParam : DEFAULT_OFFSET;
            const limit = limitParam ? limitParam : DEFAULT_LIMIT;


            console.log("domain", domain)

            //set all values in local storage
            localStorage.setItem("category", category);
            localStorage.setItem("keyword", keyword);
            localStorage.setItem("offset", offset);
            localStorage.setItem("limit", limit);

            $("#category").val(category);
            $("#keyword").val(keyword);


            const fetchUrlWifi = generateFetchUrl(
                "Wifi",
                category,
                keyword,
                offset,
                limit
            );

            const fetchUrlMobile = generateFetchUrl(
                "Mobile",
                category,
                keyword,
                offset,
                limit
            );

            $("#loader").show();

            if (!loadMore) {
                $("#container").html("");
            }

            $.when(
                $.ajax({
                    url: fetchUrlMobile,
                    method: 'POST',
                    dataType: "json",
                    timeout: 10000,
                    crossDomain: true
                }),
                $.ajax({
                    url: fetchUrlWifi,
                    method: 'POST',
                    dataType: "json",
                    timeout: 10000,
                    crossDomain: true
                })
            ).done(async function(response1 = [], response2 = []) {
                let data = response2[0]

                if (domain !== 'Wifi') {
                    console.log("inside");
                    data = response1[0].concat(response2[0]);
                }


                console.log(data);
                console.log("outside domain:", domain);


                let cardText = "";
                let containerText = "";
                if (data && (data.length || domain === "Circle")) {
                    let takeData = data;
                    if (domain === "Circle") {
                        takeData = prepareList(data);
                    }

                    for (let i = 0; i < takeData.length; i++) {
                        const item = takeData[i];
                        cardText = await createCard(item, domain);
                        containerText += cardText;
                        $("#container").append(cardText);
                        $("#loader").hide();
                    }

                    if (domain) {
                        localStorage.setItem(domain, containerText);
                        localStorage.setItem(domain + "_KEYWORD", keyword);
                        localStorage.setItem(domain + "_CATEGORY", category);
                    }
                } else {
                    //$("#container").html("<strong>No movies</strong>");
                    $("#loader").hide();
                }
            }).fail(function() {
                console.error('One or both AJAX calls failed.');
                $("#loader").hide();
            });

        }

        function search(
            domainParam = "",
            categoryParam = "",
            keywordParam = "",
            offsetParam = "",
            limitParam = "",
            loadMore = false
        ) {
            const domain = domainParam && !ON_MOBILE ? domainParam : $("#domain").val() ? $("#domain").val() : DEFAULT_DOMAIN;
            const category = domain === 'Wifi' ? categoryParam ? categoryParam : $("#category").val() ? $("#category").val() : "" : "";
            const keyword = keywordParam ? keywordParam : $("#keyword").val();
            const offset = offsetParam ? offsetParam : DEFAULT_OFFSET;
            const limit = limitParam ? limitParam : DEFAULT_LIMIT;

            //set all values in local storage
            localStorage.setItem("domain", domain);
            localStorage.setItem("category", category);
            localStorage.setItem("keyword", keyword);
            localStorage.setItem("offset", offset);
            localStorage.setItem("limit", limit);

            $("#domain").val(domain);
            $("#category").val(category);
            $("#keyword").val(keyword);


            if (keyword) {
                gtag('event', 'search_movie', {
                    'movie_title': keyword, // Movie title parameter
                    'event_category': 'search_movies', // Group under 'movies' category
                    'event_label': keyword, // Use the title as the event label
                    'value': 1 // Numeric value to track click count
                });


                gtag('event', 'domain_movie', {
                    'movie_title': domain, // Movie title parameter
                    'event_category': 'domain_movies', // Group under 'movies' category
                    'event_label': domain, // Use the title as the event label
                    'value': 1 // Numeric value to track click count
                });
            }

            const currentCardText = $("#container").html();

            $("#loader").show();

            if (domain && !loadMore) {
                const movieItems = localStorage.getItem(domain);

                if (
                    movieItems &&
                    keyword === localStorage.getItem(domain + "_KEYWORD") &&
                    category === localStorage.getItem(domain + "_CATEGORY")
                ) {
                    $("#container").html(movieItems);
                    $("#loader").hide();

                    return true;
                }
            }

            const fetchUrl = generateFetchUrl(
                domain,
                category,
                keyword,
                offset,
                limit
            );


            if (!loadMore) {
                $("#container").html("");
            }

            $.ajax({
                url: fetchUrl,
                type: (domain === "Circle") ? "GET" : "POST",
                dataType: "json",
                timeout: 10000,
                success: async function(data) {
                    let cardText = "";
                    let containerText = "";
                    if (data && (data.length || domain === "Circle")) {
                        let takeData = data;
                        if (domain === "Circle") {
                            takeData = prepareList(data);
                        }

                        for (let i = 0; i < takeData.length; i++) {
                            const item = takeData[i];
                            cardText = await createCard(item, domain);
                            containerText += cardText;
                            $("#container").append(cardText);
                            $("#loader").hide();
                        }

                        if (domain) {
                            localStorage.setItem(domain, containerText);
                            localStorage.setItem(domain + "_KEYWORD", keyword);
                            localStorage.setItem(domain + "_CATEGORY", category);
                        }
                    } else {
                        //$("#container").html("<strong>No movies</strong>");
                        $("#loader").hide();
                    }
                },
                error: function(xhr, status, error) {
                    $("#container").html("<strong>Error on fetching movies</strong>");
                    $("#loader").hide();
                },
            });
        }

        function perepareListForSeries(content) {
            try {
                const takeArr = [];
                let counter = 0;
                content?.forEach(post => {

                    takeArr[counter] = {
                        id: counter,
                        video: post.link,
                        title: post.title,
                    };
                    counter++;

                });
                return takeArr;
            } catch (e) {
                console.log(e);
            }
        }

        function prepareListForSeries(content, image) {
            try {
                const takeArr = [];
                let counter = 0;

                content?.forEach(post => {
                    takeArr[counter] = {
                        id: counter,
                        ...(image ? {
                            poster: `http://new.circleftp.net:5000/uploads/${image}`
                        } : {}), // Properly spread conditionally
                        video: post.link,
                        title: post.title,
                        circle: 1
                    };
                    counter++;
                });

                return takeArr;
            } catch (e) {
                console.log(e);
            }
        }

        function prepareList(content) {
            try {
                const takeArr = [];
                let counter = 0;
                content?.posts.forEach(post => {
                    if (post.type === 'singleVideo' || post.type === 'series') {
                        takeArr[counter] = {
                            id: counter,
                            video: post.id,
                            title: post.title,
                            poster: `http://new.circleftp.net:5000/uploads/${post.image}`,
                            cat: 'all',
                            circle: 1,
                            type: post.type,
                        };
                        counter++;
                    }
                });
                return takeArr;
            } catch (e) {
                console.log(e);
            }

        }

        function generateFetchUrl(domain, category, keyword, offset, limit) {
            let httpOrHttps = 'http:';
            try {
                httpOrHttps = window.location.protocol ?? 'http:';
            } catch (e) {
                httpOrHttps = 'http:';
            }


            let apiUrl = `${httpOrHttps}//jbmovies.rf.gd/icc.php?`;

            let keywordParam = 'keyword';

            if (domain === "Circle") {
                apiUrl = `${httpOrHttps}//new.circleftp.net:5000/api/posts?`;
                keywordParam = "searchTerm";
            } else if (domain === "Mobile") {
                apiUrl = `${httpOrHttps}//jbmovies.rf.gd/following_search.php?`;
            }

            return (
                apiUrl +
                "&" + keywordParam + "=" +
                keyword +
                "&category=" +
                category +
                "&offset=" +
                offset +
                "&limit=" +
                limit +
                "&order=desc"
            );
        }

        function prepareTitle(title, del = 1) {
            if (!title || (title && Number.isInteger(title))) return "";

            try {
                if (title && title?.endsWith("/")) {
                    del = 2;
                }

                const array = title.split("/");

                if (del === 2) {
                    const title = decodeURIComponent(array[array.length - del]);
                    try {
                        const titleReplaced = title.replace(/-/gi, " ");

                        let words = titleReplaced.toLowerCase().split(" ");
                        for (let i = 0; i < words.length; i++) {
                            words[i] = words[i][0].toUpperCase() + words[i].slice(1);
                        }
                        return words.join(" ");
                    } catch (e) {}
                }
                return decodeURIComponent(array[array.length - del]);
            } catch (e) {
                console.log(e);
            }
        }

        async function getLinkFromWeb(link, domain) {
            let httpOrHttps = 'http:';
            try {
                httpOrHttps = window.location.protocol ?? 'http:';
            } catch (e) {
                httpOrHttps = 'http:';
            }

            let apiUrl = `${httpOrHttps}//jbmovies.rf.gd`;

            if (domain === "Mobile") {
                apiUrl += "/following_get_video_url.php";
            } else if (domain === "Circle") {
                apiUrl = 'http://new.circleftp.net:5000/api/posts/' + link;
            }



            const fetchUrl = apiUrl + "?url=" + link;

            try {
                const data = await $.ajax({
                    url: fetchUrl,
                    type: (domain === "Circle") ? "GET" : "POST",
                    timeout: 10000,
                });



                return data ? (data.content && typeof data.content !== 'string') ? {
                    ...data.content,
                    image: data.image ? data.image : ''
                } : data.content ? data.content : data : link;
            } catch (error) {
                console.error("Error fetching image:", error);
                return null; // or throw error if needed
            }
        }

        async function seriesLink(link, domain) {
            try {
                $("#loader").show();

                const linkToCopy =
                    domain === "Mobile" || domain === "Circle" ?
                    await getLinkFromWeb(link, domain) :
                    link;



                if (linkToCopy && linkToCopy[0] && linkToCopy[0].episodes) {
                    const episodes = linkToCopy[0].episodes;



                    // Corrected function name
                    const items = prepareListForSeries(episodes, linkToCopy.image ? linkToCopy.image : '');

                    // Corrected the condition to check if items is empty
                    if (!items || items.length === 0) {
                        alert("No video");
                        return true;
                    }

                    if (items) {
                        $("#container").html("");
                        for (const item of items) {
                            const cardText = await createCardForSeries(item, "Circle", true);
                            $("#container").append(cardText);
                        }
                    }
                }

                $("#loader").hide(); // Hide the loader after processing

            } catch (e) {
                console.log(e);
                $("#loader").hide(); // Ensure loader is hidden in case of error
            }
        }

        async function createCardForSeries(item, domain) {
            const title = item.title ? item.title : "n/a";

            const movie = await movieDetails(title);
            //const imageSrc = item.poster ? item.poster : movie?.poster_path;

            let itemPoster = movie?.poster_path;

            if (item.poster) {
                itemPoster = replaceImageDomain(item.poster);
            }

            const imageSrc = itemPoster;

            const link = item.video ? item.video : '';

            if (item.domain) {
                domain = item.domain;
            }

            const playBtnText = `<button

                                      onclick="copyOrPlayLink('${decodeURIComponent(title).replace(/'/g, "\\'")}', '${link}', '${domain}', true, true, this);"
                                      type=""
                                      class="playBtn rounded-md bg-green-600 p-2 px-3 md:px-3 md:py-2 md:text-sm text-xs font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                      >
                                      &#9658;
                                      </button>`;

            const copyBtnText = `

                                      <button

                                      onclick="copyOrPlayLink('${decodeURIComponent(title).replace(/'/g, "\\'")}', '${link}', '${domain}', false, true, this);"
                                      type=""
                                      class="copyBtn rounded-md bg-indigo-600 md:px-3 p-2 md:py-2 md:text-sm text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                      >
                                      Copy link
                                      </button>
              `;


            let buttonText = `${playBtnText} ${copyBtnText}`;



            const cardHTML = `

                          <div class="boxShadow md:max-w-xs max-w-md mx-1 my-5 bg-[#555555] rounded-lg overflow-hidden movie pb-2">
                              <img class="w-full h-60 object-contain object-center" src="${imageSrc}" alt="Card Image">
                              <div class="md:p-6 bg-[#555555] text-center mt-1 p-5">
                              <div class="md:text-xl text-xs font-semibold mb-2 text-white">${title}</div>

                              <div class="mt-4">
                                      ${buttonText}
                              </div>
                              <div class="message">Link copied</div>
                              </div>
                          </div>

                      `;
            return cardHTML;
        }

        async function copyOrPlayLink(title, link, domain, open = false, direct = false, obj = null) {
            try {

                $(obj).parent().parent().find(".copyBtn").css('background-color', '#FFC107');

                $(obj).parent().parent().find(".copyBtn").html("Processing...");


                $("#loader").show();


                let linkToCopy =
                    (domain === "Mobile" || domain === "Circle") && !direct ?
                    await getLinkFromWeb(link, domain) :
                    link;

                if (open && window.innerWidth < 1024) {
                    gtag('event', 'play_movie', {
                        'movie_title': title, // Movie title parameter
                        'event_category': 'play_movies', // Group under 'movies' category
                        'event_label': title, // Use the title as the event label
                        'value': 1 // Numeric value to track click count
                    });


                    //window.location.href = "vlc://" + linkToCopy;

                    //https://az23.b-cdn.net/s2/upload/videos/2025/01/%5BFibwatch.Com%5DSonic.The.Hedgehog.3.(2024).WEB.DL.%5BHindi.English%5D.1080p.mkv
                    //window.location.href = `intent://${linkToCopy}#Intent;package=org.videolan.vlc;scheme=http;end`;
                    //window.location.href = linkToCopy;

                    linkToCopy = linkToCopy.replace(/\[/g, '%5B').replace(/\]/g, '%5D');
                    $("#loader").hide();
                    if (/iPhone/i.test(navigator.userAgent)) {
                        window.location.href = "vlc://" + linkToCopy;
                    } else {
                        linkToCopy = linkToCopy.replace(/https/, 'vlc');
                        linkToCopy = linkToCopy.replace(/http/, 'vlc');

                        console.log(linkToCopy);
                        window.location.href = linkToCopy;
                    }


                    return true;
                }

                gtag('event', 'copy_movie_url', {
                    'movie_title': title, // Movie title parameter
                    'event_category': 'play_movies', // Group under 'movies' category
                    'event_label': title, // Label event as 'movie title'
                    'value': 1 // Numeric value for click count
                });

                try {
                    navigator.clipboard
                        .writeText(linkToCopy)
                        .then(() => console.log("Link copied to clipboard"))
                        .catch((err) => {
                            $("#linkInput").val(linkToCopy);
                            $("#link").show();


                            var textarea = document.getElementById("linkInput");
                            textarea.setSelectionRange(0, textarea.value.length);
                            textarea.focus();
                        });
                    setTimeout(function() {
                        showFlashMessage("If VLC is installed, click play to stream the video.");
                        $(obj).parent().parent().find(".copyBtn").css('background-color', '#FF5722');
                        $(obj).parent().parent().find(".copyBtn").html("Link copied");
                        $("#loader").hide();
                    }, 3000); // 3000 milliseconds = 3 seconds

                } catch (e) {

                    gtag('event', 'error', {
                        'event_category': 'JavaScript Errors', // Group errors under this category
                        'event_label': e.message, // The error message
                        'error_type': e.type, // Custom parameter for the type of error
                        'value': 1 // Set value to 1 for each error occurrence
                    });

                    $("#linkInput").val(linkToCopy);
                    $("#link").show();


                    var textarea = document.getElementById("linkInput");
                    textarea.setSelectionRange(0, textarea.value.length);
                    textarea.focus();
                    document.execCommand('copy');

                    $("#link").hide();



                    //$(obj).parent().parent().find(".message").fadeIn();


                    setTimeout(function() {
                        //$(obj).parent().parent().find(".message").fadeOut();
                        $(obj).parent().parent().find(".copyBtn").css('background-color', '#FF5722');
                        $(obj).parent().parent().find(".copyBtn").html("Link copied");
                        $("#loader").hide();
                    }, 3000); // 3000 milliseconds = 3 seconds

                    //alert("Link copied");
                }


            } catch (e) {
                gtag('event', 'error', {
                    'event_category': 'JavaScript Errors', // Group errors under this category
                    'event_label': e.message, // The error message
                    'error_type': e.type, // Custom parameter for the type of error
                    'value': 1 // Set value to 1 for each error occurrence
                });
                console.log(e);

                $("#loader").hide();
            }
        }

        const genreLookup = {
            28: "Action",
            12: "Adventure",
            16: "Animation",
            35: "Comedy",
            80: "Crime",
            99: "Documentary",
            18: "Drama",
            10751: "Family",
            14: "Fantasy",
            36: "History",
            27: "Horror",
            10402: "Music",
            9648: "Mystery",
            10749: "Romance",
            878: "Science Fiction",
            10770: "Web",
            53: "Thriller",
            10752: "War",
            37: "Western"
        };

        // Function to look up genre names based on an array of genre ids
        function getGenresByIds(ids) {
            const genres = ids.map(id => genreLookup[id]).filter(genre => genre !== undefined);
            return genres;
        }

        async function movieDetails(title) {
            const apiUrl = "https://api.themoviedb.org";

            let videoTitle = processTitleToFetchImage(title);

            if (videoTitle) {
                videoTitle = videoTitle.replace(/([a-z])([A-Z])/g, "$1 $2");
                videoTitle = videoTitle.toLowerCase().replace(/ hindi /gi, "");
                videoTitle = videoTitle.toLowerCase().replace(/dubbed/gi, "");
                videoTitle = videoTitle.toLowerCase().replace(/ season/gi, "");
                videoTitle = videoTitle.toLowerCase().replace(/ complete/gi, "");
                videoTitle = videoTitle.toLowerCase().replace(/ bengali/gi, "");
                videoTitle = videoTitle.toLowerCase().replace(/ \d+/gi, "");
            }

            const fetchUrl =
                apiUrl +
                `/3/search/movie?query=${videoTitle}&api_key=b19e4a9a2f31ec4502883f1bb950ace1`;

            try {
                const data = await $.ajax({
                    url: fetchUrl,
                    type: "GET",
                    dataType: "json",
                    timeout: 10000,
                });

                const imageUrl = data.results[0]?.poster_path;
                const finalUrl =
                    imageUrl && videoTitle && videoTitle !== "null" ?
                    `https://image.tmdb.org/t/p/w300${imageUrl}` :
                    "https://jyotirmoy430.github.io/api/unnamed.jpg";

                const result = data.results[0] ? data.results[0] : [];

                try {
                    return {
                        ...result,
                        poster_path: finalUrl,
                        ratings: result["vote_average"] ? (Math.floor(result["vote_average"] * 10) / 10).toFixed(1) : NaN,
                        genre: getGenresByIds(result["genre_ids"].slice(0, 3))
                    };
                } catch (error) {
                    return {
                        ...result,
                        poster_path: finalUrl
                    };
                }

                //return finalUrl;
            } catch (error) {
                console.error("Error fetching image:", error);
                return null; // or throw error if needed
            }
        }

        function processTitleToFetchImage(str) {
            // Regular expression to match movie name from various formats
            const movieNameRegex = /^(.*?)\s\(\d{4}\)|^(.*?)\.\d{4}|\s\((.*?)\s/;

            const match = str.match(movieNameRegex);
            if (match && (match[1] || match[2] || match[3])) {
                // Choose the first non-empty captured group as the movie name
                const movieName = match[1] || match[2] || match[3];
                // Replace special characters, trim extra spaces, and replace dots with spaces
                return movieName
                    .replace(/[^\w\s]/g, "")
                    .trim()
                    .replace(/\./g, " ");
            } else {
                return null; // Movie name not found or too short
            }
        }

        function replaceImageDomain(url) {
            // let oldDomain = new URL(url).host;
            // let newDomain = "er56.b-cdn.net";

            let itemPoster = url.replace("https://fb45.b-cdn.net", "https://er56.b-cdn.net")
            itemPoster = itemPoster.replace("https://thn45.b-cdn.net", "https://er56.b-cdn.net")

            return itemPoster;
        }

        async function createCard(item, domain, action = false) {
            try {
                const title = prepareTitle(item.title ? item.title : item.video);

                console.log(item);

                const link = item.video ? item.video : item.href ? item.href : "";
                const movie = await movieDetails(title);

                console.log(movie)

                let itemPoster = movie?.poster_path;

                if (item.poster) {
                    itemPoster = replaceImageDomain(item.poster);
                }

                const imageSrc = itemPoster;

                //const imageSrc = movie?.poster_path;

                const {
                    id,
                    video,
                    poster,
                    cat,
                    href,
                    following,
                    circle
                } = item;

                const tabindex = id + 4;
                //console.log(itemString.toString());

                if (following === "1") {
                    domain = "Mobile";
                }

                if (item.domain) {
                    domain = item.domain;
                }

                const playBtnText = `<button

                                      onclick="copyOrPlayLink('${decodeURIComponent(title).replace(/'/g, "\\'")}', '${link}', '${domain}', true, '', this);"
                                      type=""
                                      class="playBtn rounded-md bg-green-600 p-2 px-3 md:px-3 md:py-2 md:text-sm text-xs font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                      >
                                      &#9658;
                                      </button>`;

                const copyBtnText = `

                                      <button

                                      onclick="copyOrPlayLink('${decodeURIComponent(title).replace(/'/g, "\\'")}', '${link}', '${domain}', '', '', this);"
                                      type=""
                                      class="copyBtn rounded-md bg-indigo-600 md:px-3 p-2 md:py-2 md:text-sm text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                      >
                                      Copy link
                                      </button>
              `;

                const watchLaterBtnText = `<button

                                      onclick="addToWatchLaterList('${id}', '${decodeURIComponent(title)}', '${href}', '${video}', '${poster}','${cat}', '${following}', '${circle}', '${item.type ? item.type : 0}')"
                                      type=""
                                      class="rounded-md bg-indigo-600 p-2 md:px-3 md:py-2 md:text-sm text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                      >
                                      &#9733;
                                      </button>

                                      ${action ?
                                      `
                                      <button

                                      onclick="removeFromWatchLaterList('${id}')"
                                      type=""
                                      class="rounded-md bg-indigo-600  md:px-3 p-2 md:py-2 text-xs md:text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                      >
                                      &#9734;
                                      </button>
                                      `
                                          : ""
                                      }`

                const seriesButtonText = `<button

                                      onclick="seriesLink('${link}', '${domain}');"
                                      type=""
                                      class="rounded-md bg-red-600 p-2 px-3 md:px-3 md:py-2 md:text-sm text-xs font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                      >
                                      &#9658; Watch series
                                      </button>`;


                let buttonText = `${playBtnText} ${copyBtnText} ${watchLaterBtnText}`;
                if (item.type && item.type === 'series') {
                    buttonText = `${seriesButtonText} ${watchLaterBtnText}`;
                }

                let rattingsText = ``;
                if (movie && movie.ratings && movie.ratings != NaN && movie.ratings != 'NaN') {
                    rattingsText = `<div class="ratings flex"><div style="font-weight:bold;">${movie.ratings}</div><div style="margin-left:5px;">&#9733;</div></div>`;
                }

                let genreText = ``;
                let genreTextChild = ``;

                if (movie && movie.genre) {
                    movie?.genre?.forEach(genre => {
                        genreTextChild = genreTextChild + `<div class="genre">${genre}</div>`;
                    });
                    genreText = `<div class="genres flex justify-around">${genreTextChild}</div>`;
                }



                const cardHTML = `

                          <div class="boxShadow md:max-w-xs max-w-md mx-1 my-5 bg-[#555555] overflow-hidden movie pb-2 relative" tabindex="${tabindex}">
                              <div class="relative">
                              <img class="w-screen md:w-full object-contain object-center" style="min-height:190px;" src="${imageSrc}" alt="${title}">
                              ${rattingsText}
                              ${genreText}
                              </div>
                              <div class="md:p-6 bg-[#555555] text-center mt-1 p-5">
                              <div class="md:text-xl text-xs font-semibold mb-2 text-white">${title}</div>

                              <div class="mt-4">
                                      ${buttonText}
                              </div>
                              <div class="message">Link copied</div>
                              </div>
                          </div>

                      `;
                return cardHTML;
            } catch (e) {
                console.log(e);
            }

        }


        const inputBox = document.getElementById('keyword');

        inputBox.addEventListener('keydown', (event) => {
            if (['Enter'].includes(event.key)) {
                event.preventDefault(); // Prevent default form submission or scrolling

                if (event.key === 'Enter') {
                    search();
                    return;
                }

                return;
            }
        });

        function showFlashMessage(message) {
            let flash = document.getElementById("flashMessage");
            flash.innerText = message;
            flash.style.top = "20px"; // Slide down

            // Hide after 2 seconds
            setTimeout(() => {
                flash.style.top = "-100px"; // Slide up
            }, 4000);
        }
    </script>
</body>

</html>