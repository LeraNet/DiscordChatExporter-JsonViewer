<?php
header("Cache-Control: public, max-age=86400");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="description" content="Leranet Discord Emulator">
    <meta name="author" content="Leranet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: #36393F;
            color: #DCDDDE;
            font-family: Arial, sans-serif;
            padding: 20px;
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        .server-info {
            display: none;
        }

        .info-card {
            background-color: #3F454A;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.25);
            width: 95%;
            z-index: 1;
            transition: 0.4s all ease-in-out;
        }

        .info-card .part {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .info-card .part h1 {
            margin: 0;
            font-size: 20px;
        }

        .info-card .part h2 {
            margin: 0;
            font-size: 15px;
        }

        .info-card .part img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .info-card h1,
        .info-card h2 {
            margin: 0;
        }

        .info-card a {
            color: #DCDDDE;
            text-decoration: none;
            font-size: 20px;
            position: relative;
        }

        .info-card a:hover {
            text-decoration: underline;
        }

        .info-card .openable {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            width: 50vw;
            height: 50vh;
            background-color: #36393F;
            z-index: 2;
        }

        .info-card .openable iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .message {
            background-color: #40444B;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            position: relative;
        }

        .message-content {
            margin-left: 50px;
            margin-top: 0px;
            font-weight: 300;
            white-space: pre-wrap;
            width: auto;
        }

        .username {
            font-weight: bold;
            display: inline;
            margin-bottom: 5px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .timestamp {
            display: inline;
            font-size: 12px;
            color: #72767D;

        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #7289DA;
        }

        .pagination a:hover {
            text-decoration: underline;
        }

        .server-info {
            background-color: #40444B;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .server-info img {
            border-radius: 50%;
            margin: auto;
            height: 20vh;
            display: block;
        }

        .server-info h1,
        h2 {
            display: block;
            text-align: center;
        }

        .server-info p {
            display: block;
            text-align: center;
        }


        .attachmentk {
            width: auto;
            max-width: 100%;
            height: 75vh;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .embed {
            background-color: #36393f;
            padding: 10px;
            margin-bottom: 5px;
            width: fit-content;
            border-radius: 10px;
        }

        .embed-title {
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
        }

        .embed-description {
            color: #ddd;
            margin-bottom: 5px;
            white-space: pre;
        }

        .embed-thumbnail {
            border-radius: 10px;
            height: 30vh;
        }

        .embed-author {
            font-size: 8px;
        }

        .sticker {
            max-width: 100px;
            height: auto;
            margin-bottom: 5px;
        }

        .reactions {
            display: flex;
            margin-top: 10px;
        }

        .reaction {
            width: auto;
            height: 10px;
            margin-right: 5px;
            background-color: rgba(0, 0, 0, 0.25);
            padding: 10px;
            border-radius: 10px;
            display: inline-flex;
            justify-content: flex-start;
            align-items: center;
            gap: 5px;
        }

        .reaction img {
            height: 15px;
        }

        .mention {
            font-weight: bold;
            color: #fff;
            margin-right: 5px;
        }

        .pagination {
            margin-top: 10px;
            background-color: #36393F;
            color: white;
            padding: 10px;
            border-radius: 10px;
            display: inline;
        }

        .pagination-button {
            margin-top: 10px;
            background-color: #36393F;
            color: white;
            padding: 10px;
            border-radius: 10px;
            border-width: 1px;
            border-style: solid;
            border-color: -internal-light-dark(rgb(118, 118, 118), rgb(133, 133, 133));
            display: inline;
        }

        .pinned {
            background-color: green;
            border-radius: 10px;
            color: white;
            padding: 5px;
        }

        a {
            color: cyan;
            cursor: pointer;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
            cursor: pointer;
        }

        .ping {
            background-color: blue;
            padding: 3px;
            border-radius: 3px;
        }

        code {
            background-color: #202225;
            display: block;
            padding: 10px;
            border-radius: 10px;
            white-space: pre;
        }

        .mentions {
            background-color: #202225;
            display: block;
            width: fit-content;
            padding: 10px;
            border-radius: 0px 0px 10px 10px;
            white-space: pre;
            margin: 1px;
        }

        .tenor-gif-embed {
            max-height: 40vh;
            width: fit-content;
        }

        .file {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.25);
            border-radius: 10px;
            margin: 10px;
            width: fit-content;
            border: 2px solid rgba(0, 0, 0, 0.5);
        }

        .file i {
            font-size: 25px;
            color: white;
        }

        @media screen and (max-width: 600px) {
            .info-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-card .part {
                margin-bottom: 10px;
            }

            .info-card .part h1 {
                font-size: 15px;
            }

            .info-card .part h2 {
                font-size: 10px;
            }

            .info-card .part img {
                width: 30px;
                height: 30px;
            }

            .info-card a {
                font-size: 15px;
            }

            .info-card .openable {
                width: 100%;
                height: 50vh;
            }

            .message-content {
                margin-left: 40px;
            }

            .avatar {
                width: 30px;
                height: 30px;
            }

            .timestamp {
                font-size: 10px;
            }

            .server-info img {
                height: 10vh;
            }

            .server-info h1,
            h2 {
                font-size: 10px;
            }

            .server-info p {
                font-size: 10px;
            }

            .attachmentk {
                height: 50vh;
            }

            .embed-thumbnail {
                height: 20vh;
            }

            .sticker {
                max-width: 50px;
            }

            .reaction img {
                height: 10px;
                margin: 2px;
            }

            .pagination {
                font-size: 10px;
            }

            .pagination-button {
                font-size: 10px;
            }

            .ping {
                font-size: 10px;
            }

            code {
                font-size: 10px;
            }

            .mentions {
                font-size: 10px;
            }
        }

        .margin-div {
            height: 60px;
        }

        .top {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
            background-color: #7289DA;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.25);
        }

        .top i {
            font-size: 32px;
            color: white;
            transition: 0.4s all ease-in-out;
        }

        .top .bottom {
            transform: rotate(180deg);
        }

        .contentk h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
            text-align: left;
        }

        .audio {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.25);
            border-radius: 10px;
            margin: 10px;
            width: fit-content;
            border: 2px solid rgba(0, 0, 0, 0.5);
        }

        .audio legend {
            font-size: 20px;
            color: white;
            font-weight: bold;
            background-color: #7289DA;
            padding: 5px;
            border: 2px solid rgba(0, 0, 0, 0.5);
            border-radius: 5px 5px 0px 0px;

        }

        .audio i {
            font-size: 25px;
            color: white;
        }

        .audio-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .audio-progress {
            background-color: white;
            display: block;
            height: 5px;
            width: 100px;
            border-radius: 5px;
            position: relative;
        }

        .audio-progress .real {
            position: absolute;
            background-color: #7289DA;
            display: block;
            height: 5px;
            width: 0px;
            border-radius: 5px;
            top: 0;
            
        }

        .audio-time {
            color: white;
        }

        .audio-buttons {
            display: flex;
            align-items: center;
        }

        .audio-play,
        .audio-pause {
            border: none;
            color: #FFFFFF;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            padding: 10px 15px;
            transition: all 0.2s ease;
            background-position: center;
            background-size: 200% 200%;
        }

        .audio-play {
            border-radius: 5px 0px 0px 5px;
            background-image: linear-gradient(90deg, green, #7289DA);
        }

        .audio-pause {
            border-radius: 0px 5px 5px 0px;
            background-image: linear-gradient(90deg, #7289DA, red);
        }

        .audio-play:hover,
        .audio-pause:hover {
            background-color: #7289DA;
            transform: scale(1.1);
        }

        .audio-play:active,
        .audio-pause:active {
            background-color: #7289DA;
            transform: scale(0.9);
        }

        .audio-play i,
        .audio-pause i {
            font-size: 20px;
        }

        <?php if (isset($_GET["include"])): ?>
            .info-card {
                display: none;
            }

            .devmsg {
                display: none;
            }

            .page {
                display: none;
            }

            .margin-div {
                display: none;
            }

        <?php endif; ?>

        <?php if (isset($_GET["animate"])): ?>
            animate {
                opacity: 0;
                animation: fadein 1s forwards ease-in-out 3s;
            }

            .info-card {
                top: -100px;
                animation: comebumpstay 1s forwards ease-in-out 1s;
            }

            @keyframes fadein {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes comebumpstay {
                0% {
                    top: -100px;
                }

                50% {
                    top: 25px;
                }

                75% {
                    top: 5px;
                }

                100% {
                    top: 10px;
                }
            }

        <?php endif; ?>
    </style>
</head>

<body>
    <div class="margin-div"></div>
    <animate>
        <?php include "emulatorHandler.php" ?>
    </animate>
    <title>LDE -
        <?= $serverInfo["name"] ?> /
        <?= $serverInfo["channel"] ?>
    </title>
    <div class="info-card">
        <div class="part">
            <a href="/emulator/jsons.php"><i class="fas fa-arrow-left"></i></a>
            <img src="<?= $serverInfo["icon"] ?>" alt="" class="logo">
            <h2>
                <?= $serverInfo["name"] ?> /
                <?= $serverInfo["channel"] ?>
            </h2>
        </div>
        <div class="part">
            <h1>LERANET DISCORD EMULATOR</h1>
        </div>
        <div class="part">
            <a><i class="fas fa-thumbtack"></i>
                <div class="openable">
                    <iframe loading="lazy" src="/tools/json.php?path=<?= $_GET["path"] ?>&pinOnly=true&include=true"
                        frameborder="0" style="width: 100%; height: 100%;"></iframe>
                </div>
            </a>
            <?php
            if ($serverInfo["name"] == "Makarena (kapandı)") {
                ?>
                <a><i class="fas fa-user"></i>
                    <div class="openable">
                        <iframe loading="lazy" src="/tools/users.php" frameborder="0"
                            style="width: 100%; height: 100%;"></iframe>
                    </div>
                </a>
                <a><i class="fas fa-search"></i>
                    <div class="openable">
                        <iframe loading="lazy" src="/tools/search-ui-json.php?spath=<?= $_GET["path"] ?>" frameborder="0"
                            style="width: 100%; height: 100%;"></iframe>
                    </div>
                </a>
            <?php } ?>
            <a><i class="fas fa-question-circle"></i>
                <div class="openable">
                    <iframe loading="lazy" src="/emulator/settings.php" frameborder="0"
                        style="width: 100%; height: 100%;"></iframe>
                </div>
            </a>
        </div>
    </div>
    <div class="top" style="cursor: pointer">
        <i class="fas fa-arrow-up"></i>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        window.addEventListener("message", function (event) {
            if (event.data == "reload") {
                location.reload();
            }
        });

        $(window).scroll(function () {
            $("img").each(function () {
                if ($(this).offset().top < $(window).scrollTop() + $(window).height() + 100) {
                    $(this).attr("src", $(this).attr("data-src"));
                }
            });
        });

        $(".attachmentk").click(function () {
            if ($(this).attr("src").includes(".png") || $(this).attr("src").includes(".jpg") || $(this).attr("src").includes(".gif")) {
                window.open($(this).attr("src"));
            }
        });

        var lastScrollTop = 0;
        $(window).scroll(function (event) {
            var st = $(this).scrollTop();
            if (st < lastScrollTop && st > 150) {
                $(".info-card").css("top", "10px");
            } else if (st > lastScrollTop && st > 150) {
                $(".info-card").css("top", "-100px");
                $(".openable").slideUp();
            }
            lastScrollTop = st;
        });

        var openableopenned = false;
        $(".openable").hide();
        $(".info-card a").click(function () {
            if (openableopenned) {
                $(".openable").slideUp();
                openableopenned = false;
            } else {
                $(this).children(".openable").slideDown();
                openableopenned = true;
            }
        });

        $(document).click(function (event) {
            if (!$(event.target).closest(".openable").length && !$(event.target).closest(".info-card a").length) {
                if (openableopenned) {
                    $(".openable").slideUp();
                    openableopenned = false;
                }
            }
        });

        $("iframe").each(function () {
            var iframe = $(this);
            iframe.on("load", function () {
                iframe.contents().find("a").each(function () {
                    var a = $(this);
                    a.click(function (event) {
                        event.preventDefault();
                        window.open(a.attr("href"));
                    });
                });
            });
        });

        var site50precent = $(document).height() / 2;
        var dontautorotate = false;

        $(window).scroll(function () {
            if (dontautorotate) {
                setTimeout(function () {
                    dontautorotate = false;
                }, 1000);
                return;
            }
            if ($(window).scrollTop() > site50precent) {
                $(".top i").removeClass("bottom");
            } else {
                $(".top i").addClass("bottom");
            }
        });

        if ($(window).scrollTop() > site50precent) {
            $(".top i").removeClass("bottom");
        } else {
            $(".top i").addClass("bottom");
        }

        $(".top").click(function () {
            if ($(".top i").hasClass("bottom")) {
                $("html, body").animate({ scrollTop: $(document).height() }, 250);
                $(".top i").removeClass("bottom");
            } else {
                $("html, body").animate({ scrollTop: 0 }, 250);
                $(".top i").addClass("bottom");
            }
            dontautorotate = true;
        });

        var sc = 0;
        window.addEventListener('scroll', function () {
            if (sc == 0) {
                sc = 1;
                lottie.loadAnimation({
                    container: document.querySelector('#lottie-1'),
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: 'https://assets10.lottiefiles.com/packages/lf20_wzcckjq4.json'
                });
            }
        });
        window.onload = function () {
            window.scrollTo(window.scrollX, window.scrollY - 1);
            window.scrollTo(window.scrollX, window.scrollY + 1);
        };

        <?php if (isset($_GET["animate"])): ?>
            $("html, body").animate({ scrollTop: 0 }, 1);
        <?php endif; ?>
    </script>
</body>

</html>
