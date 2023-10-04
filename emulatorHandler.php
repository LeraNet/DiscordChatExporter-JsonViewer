<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<script type="text/javascript" async src="https://tenor.com/embed.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.9/lottie_light.min.js" integrity="sha512-ltKac/3nndizJUSC1QCJ81A63grzY2pt3tV6JoStBLdauqbN3fBpNHYVcBurVanF3e5hP3TKx/TGaoov4Y7jKw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<?php
if (isset($_GET['path'])) {
    function bolderX($thing)
    {
        $thing = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', $thing);
        $thing = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', $thing);
        $thing = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $thing);

        $thing = preg_replace('/^1\. (.*)$/m', '<ol><li>$1</li></ol>', $thing);
        $thing = preg_replace('/^2\. (.*)$/m', '<ol><li>$1</li></ol>', $thing);
        $thing = preg_replace('/^3\. (.*)$/m', '<ol><li>$1</li></ol>', $thing);

        $thing = preg_replace('/\*\*(.*)\*\*/', '<b>$1</b>', $thing);

        $thing = preg_replace('/\*(.*)\*/', '<i>$1</i>', $thing);

        return $thing;
    }

    function convertToLink($text)
    {
        $pattern = '/\[([^\]]+)\]\(([^)]+)\)/';
        $replacement = '<a href="$2">$1</a>';
        $linkedText = preg_replace($pattern, $replacement, $text);
        return $linkedText;
    }

    function convertToSpan($text)
    {
        $pattern = '/@([^\s]+)/';
        $replacement = '<span class="ping">@$1</span>';
        $spannedText = preg_replace($pattern, $replacement, $text);
        return $spannedText;
    }

    function doesHaveLink($text)
    {
        $pattern = '/(?<!href="|src=")((https?:\/\/)[^\s<]+[^<.,:;"\')\]\s])/';
        if (preg_match($pattern, $text)) {
            return true;
        } else {
            return false;
        }
    }

    function extractLinks($text)
    {
        $pattern = '/(?<!href="|src=")((https?:\/\/)[^\s<]+[^<.,:;"\')\]\s])/';
        $matches = array();
        preg_match_all($pattern, $text, $matches);
        $links = array();
        foreach ($matches[0] as $match) {
            $link = array();
            $link["url"] = $match;
            $link["text"] = $match;
            array_push($links, $link);
        }
        return $links;
    }

    $jsonPath = $_GET['path'];
    $jsonString = file_get_contents($_SERVER["DOCUMENT_ROOT"] . $jsonPath);
    $data = json_decode($jsonString, true);

    echo "<div class='server-info'>";
    echo "<h1>" . $data["guild"]["name"] . "</h1>";
    echo "<h2>" . $data["channel"]["category"] . " - " . $data["channel"]["name"] . "</h2>";
    echo "<p>" . $data["channel"]["topic"] . "</p>";
    echo "</div>";

    $languageClasses = [
        'js' => 'javascript',
        'python' => 'python',
        "md" => "md"
    ];

    $pinOnly = isset($_GET["pinOnly"]) ? $_GET["pinOnly"] : false;


    $perPage = 50;
    $page = isset($_GET['page']) ? $_GET['page'] : 0;
    $totalMessages = count($data['messages']);
    $messages = array();
    if ($pinOnly) {
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_json_cache/" . basename($_GET["path"]) . "/pins.json")) {
            $data = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_json_cache/" . basename($_GET["path"]) . "/pins.json"), true);
            $totalMessages = count($data['messages']);
            $messages = $data['messages'];
        } else {
            $data = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . $_GET["path"]), true);
            $data["messages"] = array_filter($data["messages"], function ($message) {
                return $message["isPinned"] == true;
            });
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_json_cache/" . basename($_GET["path"]) . "/pins.json", json_encode($data));
            $totalMessages = count($data['messages']);
            $messages = $data['messages'];
        }
    } else {
        $totalPages = ceil($totalMessages / $perPage);
        if ($page == 0) {
            $startIndex = $totalMessages - $perPage;
            $page = $totalPages;
        }
        $startIndex = ($page - 1) * $perPage;
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_json_cache/" . basename($_GET["path"]) . "/" . $page . ".json")) {
            $messages = json_decode(file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_json_cache/" . basename($_GET["path"]) . "/" . $page . ".json"), true);
            devprint("Loaded from cache<br>");
        } else {
            $messages = array_slice($data['messages'], $startIndex, $perPage);
            if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_json_cache/" . basename($_GET["path"]))) {
                mkdir($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_json_cache/" . basename($_GET["path"]));
            }
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_json_cache/" . basename($_GET["path"]) . "/" . $page . ".json", json_encode($messages));
            devprint("Saved to cache<br>");
        }
    }
    $serverInfo["name"] = $data["guild"]["name"];
    $serverInfo["channel"] = $data["channel"]["name"];
    $serverInfo["icon"] = "https://media.discordapp.net/attachments/996815021109674054/1100497154256142367/1095737455082225746.webp";
    if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_servericons/" . $data["guild"]["id"] . ".png")) {
        $serverInfo["icon"] = "/data/emulator_servericons/" . $data["guild"]["id"] . ".png";
    } else {
        $ifLinkStillUp = curl_init($data["guild"]["iconUrl"]);
        curl_setopt($ifLinkStillUp, CURLOPT_NOBODY, true);
        curl_exec($ifLinkStillUp);
        $statusCode = curl_getinfo($ifLinkStillUp, CURLINFO_HTTP_CODE);
        if ($statusCode == 200) {
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_servericons/" . $data["guild"]["id"] . ".png", file_get_contents($data["guild"]["iconUrl"]));
            $serverInfo["icon"] = "/data/emulator_servericons/" . $data["guild"]["id"] . ".png";
        } else {
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_servericons/" . $data["guild"]["id"] . ".png", file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_servericons/0.png"));
            $serverInfo["icon"] = "/data/emulator_servericons/" . $data["guild"]["id"] . ".png";
        }
    }
    $crt = ($page - 1) * 50;
    foreach ($messages as $message) {
        $id = $message["author"]["id"];
        $username = $message["author"]["name"];
        $avatarUrl = $message["author"]["avatarUrl"];
        $content = $message["content"];
        $timestamp = $message["timestamp"];
        $attachments = $message["attachments"];
        $embeds = $message["embeds"];
        $stickers = $message["stickers"];
        $reactions = $message["reactions"];
        $mentions = $message["mentions"];
        $roles = $message["author"]["roles"] ?? [
            [
                "name" => "No roles",
                "color" => "#000000"
            ]
        ];
        $color = $message["author"]["color"];

        foreach ($roles as $role) {
            $roleName = $role["name"];
            $roleColor = $role["color"] ?? "#000000";
            if ($roleColor != "#000000") {
                $color = $roleColor;
                break;
            }
        }

        $pfp = "";
        if (!is_file($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_pfps/$id.png")) {
            $pfp = "https://discordlookup.mesavirep.xyz/v1/user/$id";
            $pfp = json_decode(file_get_contents($pfp), true)["avatar"]["link"] ?? "https://archive.org/download/discordprofilepictures/discordred.png";
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/data/emulator_pfps/$id.png", file_get_contents($pfp));
            $pfp = "/data/emulator_pfps/$id.png";
        } else {
            $pfp = "/data/emulator_pfps/$id.png";
        }
        if (strpos($content, "https://tenor.com/view") !== false) {
            echo '<div class="message" id="' . $crt . '">';
            echo '<img loading="lazy" class="avatar" src="' . $pfp . '">';
            echo '<div class="message-content">';
            echo '<span class="username" style="color: ' . $color . '">' . $username . '</span>';
            $date = DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $timestamp);
            if ($date == false) {
                echo " Invalid timestamp format";
            } else {
                $timestamp = $date->format("d-m-Y H:i:s");
            }
            echo '<div class="timestamp">' . $timestamp . '</div>';
            echo '<div class="contentk">';
            $gif = explode("-", $content);
            $gif = $gif[count($gif) - 1];
            echo '<div class="tenor-gif-embed" data-postid="' . $gif . '" data-share-method="host" data-aspect-ratio="0.921875" data-width="50%"></div> ';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        } else if ($content == "Joined the server." || $content == "Pinned a message.") {
            echo '<div class="message" id="' . $crt . '">';
            echo '<span class="username" style="color: ' . $color . '">' . $username . '</span>';
            $date = DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $timestamp);
            if ($date == false) {
                echo " Invalid timestamp format";
            } else {
                $timestamp = $date->format("d-m-Y H:i:s");
            }
            echo '<div class="timestamp"> ' . $timestamp . '</div>';
            echo '<div class="contentk">';
            echo "<b>" . $content . "</b>";
            echo '</div>';
            echo '</div>';

        } else {
            $pinned = $message["isPinned"] == true ? "<span class='pinned'>Pinned</span>" : "";

            echo '<div class="message" id="' . $crt . '">';
            echo '<img loading="lazy" class="avatar" src="' . $pfp . '">';
            echo '<div class="message-content">';
            echo '<span class="username" style="color: ' . $color . '">' . $username . " " . $pinned . '</span>';
            $date = DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $timestamp);

            if ($date === false) {
                echo " Invalid timestamp format";
            } else {
                $formattedTimestamp = $date->format("d-m-Y H:i:s");
            }
            echo '<div class="timestamp">' . $timestamp . '</div>';
            echo '<div class="contentk">';
            $content = preg_replace('/(?<!href="|src=")((https?:\/\/)[^\s<]+[^<.,:;"\')\]\s])/', '<a href="$1" target="_blank">$1</a>', $content);

            $content = bolderX(convertToSpan($content));
            $content = preg_replace_callback('/```(.*?)\n(.*?)```/s', function ($matches) use ($languageClasses) {
                $language = strtolower(trim($matches[1]));

                if (isset($languageClasses[$language])) {
                    $code = $matches[2];
                    $class = $languageClasses[$language];
                    return '<code class="' . $class . '">' . $code . '</code>';
                }

                return $matches[0];
            }, $content);
            echo $content;
            echo '</div>';
            foreach ($attachments as $attachment) {
                $attachmentUrl = $attachment["url"];
                $attachmentUrl = explode("?", $attachmentUrl)[0];

                if (strpos($attachmentUrl, "tenor.com/view") !== false) {
                    $gifId = explode("view/", $attachmentUrl)[1];

                    $gifUrl = "https://tenor.com/view/" . $gifId;
                    echo '<div class="tenor-gif-embed" data-postid="' . $gifId . '" data-share-method="host" data-aspect-ratio="0.921875" data-width="50%"></div> ';
                } else {
                    $fileExtension = pathinfo($attachmentUrl, PATHINFO_EXTENSION);

                    $fileExtension = pathinfo($attachmentUrl, PATHINFO_EXTENSION);

                    if (in_array($fileExtension, ["mp4", "webm", "mov"])) {
                        $poster = ($_SESSION["faster_videos"] ?? "false") == "true" ? "poster='/tools/randomshitpost.php?" . rand(1, 5) . "'" : "";
                        echo '<video ' . $poster . ' "preload="none"class="attachmentk" src="' . $attachmentUrl . '" controls></video>';
                    } elseif (in_array($fileExtension, ["mp3", "wav", "ogg"])) {
                        echo '<fieldset class="audio">';
                        echo '<legend>' . basename($attachmentUrl) . '</legend>';
                        echo '<i class="fas fa-music"></i>';
                        echo '<audio class="" src="' . $attachmentUrl . '" id="audio_' . $crt . '"></audio>';
                        echo '<div class="audio-controls">';
                        echo '<div class="audio-progress" id="audio-progress-' . $crt . '">
                        <div class="real" style="width: 0%;"></div>
                        </div>';
                        echo '<div class="audio-time" id="audio-time-' . $crt . '"></div>';
                        echo '<div class="audio-buttons">';
                        echo '<button class="audio-play" onclick="document.getElementById(\'audio_' . $crt . '\').play()"><i class="fas fa-play"></i></button>';
                        echo '<button class="audio-pause" onclick="document.getElementById(\'audio_' . $crt . '\').pause()"><i class="fas fa-pause"></i></button>';
                        echo '</div>';
                        echo '</fieldset>';
                    } elseif (in_array($fileExtension, ["txt", "pdf", "doc", "docx"])) {
                        echo '<a class="attachmentk" href="' . $attachmentUrl . '" target="_blank">View File</a>';
                    } elseif (in_array($fileExtension, ["exe", "msi", "dmg"])) {
                        echo '<a class="attachmentk" href="' . $attachmentUrl . '">Download File</a>';
                    } else if (in_array($fileExtension, ["png", "jpg", "jpeg", "gif", "webp"])) {
                        echo '<img loading="lazy" class="attachmentk" src="' . $attachmentUrl . '">';
                    } else {
                        echo '<div class="file"> <i class="fas fa-file"></i> <a href="' . $attachmentUrl . '" target="_blank">' . basename($attachmentUrl) . '</a></div>';
                    }
                }
            }

            if (doesHaveLink($content)) {
                $links = extractLinks($content);

                foreach ($links as $link) {
                    $attachmentUrl = $link["url"];
                    $attachmentUrl = explode("?", $attachmentUrl)[0];

                    if (strpos($attachmentUrl, "tenor.com/view") !== false) {
                        $gifId = explode("view/", $attachmentUrl)[1];

                        $gifUrl = "https://tenor.com/view/" . $gifId;
                        echo '<div class="tenor-gif-embed" data-postid="' . $gifId . '" data-share-method="host" data-aspect-ratio="0.921875" data-width="50%"></div> ';
                    } else {
                        $fileExtension = pathinfo($attachmentUrl, PATHINFO_EXTENSION);

                        $fileExtension = pathinfo($attachmentUrl, PATHINFO_EXTENSION);

                        if (in_array($fileExtension, ["mp4", "webm", "mov"])) {
                            $poster = ($_SESSION["faster_videos"] ?? "false") == "true" ? "poster='/tools/randomshitpost.php?" . rand(1, 5) . "'" : "";
                            echo '<video ' . $poster . ' "preload="none"class="attachmentk" src="' . $attachmentUrl . '" controls></video>';
                        } elseif (in_array($fileExtension, ["mp3", "wav", "ogg"])) {
                            echo '<fieldset class="audio">';
                            echo '<legend>' . basename($attachmentUrl) . '</legend>';
                            echo '<i class="fas fa-music"></i>';
                            echo '<audio class="" src="' . $attachmentUrl . '" id="audio_' . $crt . '"></audio>';
                            echo '<div class="audio-controls">';
                            echo '<div class="audio-progress" id="audio-progress-' . $crt . '">
                            <div class="real" style="width: 0%;"></div>
                            </div>';
                            echo '<div class="audio-time" id="audio-time-' . $crt . '"></div>';
                            echo '<div class="audio-buttons">';
                            echo '<button class="audio-play" onclick="document.getElementById(\'audio_' . $crt . '\').play()"><i class="fas fa-play"></i></button>';
                            echo '<button class="audio-pause" onclick="document.getElementById(\'audio_' . $crt . '\').pause()"><i class="fas fa-pause"></i></button>';
                            echo '</div>';
                            echo '</fieldset>';
                        } elseif (in_array($fileExtension, ["txt", "pdf", "doc", "docx"])) {
                            echo '<a class="attachmentk" href="' . $attachmentUrl . '" target="_blank">View File</a>';
                        } elseif (in_array($fileExtension, ["exe", "msi", "dmg"])) {
                            echo '<a class="attachmentk" href="' . $attachmentUrl . '">Download File</a>';
                        } else if (in_array($fileExtension, ["png", "jpg", "jpeg", "gif", "webp"])) {
                            echo '<img loading="lazy" class="attachmentk" src="' . $attachmentUrl . '">';
                        }
                    }
                }
            }

            foreach ($embeds as $embed) {
                $embedTitle = bolderX(convertToLink($embed["title"] ?? ""));
                $embedURL = bolderX(convertToLink($embed["url"] ?? ""));
                $embedDescription = bolderX(convertToLink($embed["description"]));
                $embedThumbnail = $embed["thumbnail"] ?? null;
                $embedImages = $embed["images"] ?? null;
                $embedFields = $embed["fields"] ?? null;
                $embedAuthor = $embed["author"] ?? null;

                echo '<div class="embed">';
                if (isset($embedAuthor)) {
                    echo '<a class="embed-author" href="' . $embedAuthor["url"] ?? "" . '">' . $embedAuthor["name"] . '</a>';
                }
                echo '<a href="' . $embedURL . '" class="embed-title">' . $embedTitle . '</a>';
                echo '<div class="embed-description">' . $embedDescription . '</div>';
                if (isset($embedThumbnail)) {
                    echo '<img loading="lazy" class="embed-thumbnail" src=' . $embedThumbnail["url"] . '>';
                }
                if (isset($embedFields)) {
                    foreach ($embedFields as $embedField) {
                        $embedFieldName = $embedField["name"];
                        $embedFieldValue = $embedField["value"];
                        echo '<div class="embed-field"><span class="embed-field-name">' . $embedFieldName . '</span><span class="embed-field-value">' . $embedFieldValue . '</span></div>';
                    }
                }
                if (isset($embedImages)) {
                    foreach ($embedImages as $embedImage) {
                        echo '<img loading="lazy" class="embed-image" src=' . $embedImage["url"] . '>';
                    }
                }
                echo '</div>';
            }

            foreach ($stickers as $sticker) {
                $stickerUrl = $sticker["sourceUrl"];
                if (strpos($stickerUrl, ".json") !== false) {
                    echo '<div class="sticker lottie" data-url="' . $stickerUrl . '"></div>';
                } else {
                    echo '<img loading="lazy" class="sticker" src="' . $stickerUrl . '">';
                }
            }

            if (!empty($reactions)) {
                echo "<div class='reactions'>";
                foreach ($reactions as $reaction) {
                    $emojiName = $reaction["emoji"]["name"];
                    $emojiImageUrl = $reaction["emoji"]["imageUrl"];
                    $reactionCount = $reaction["count"];
                    echo '<div class="reaction"><img loading="lazy" class="" src="' . $emojiImageUrl . '" alt="' . $emojiName . '" title="' . $emojiName . '">' . $reactionCount . '</div>';
                }
                echo "</div>";
            }
            if (!empty($mentions)) {
                echo "<div class='mentions'><i class='fas fa-share'> </i>";
                foreach ($mentions as $mention) {
                    $mentionUsername = $mention["name"];
                    echo '<span class="mention">@' . $mentionUsername . '</span>';
                }
                echo "</div>";
            }

            echo '</div>';
            echo '</div>';
            $crt++;
        }
    }
    if (count($messages) == 0) {
        echo "<div class='message' id='0'>";
        echo "<div class='message-content'>";
        echo "<span class='username'>No messages found</span>";
        echo "</div>";
        echo "</div>";
    }
    if (!$pinOnly):
        ?>
        <?php if ($page != 1) { ?>
            <a id="dont" href="<?= '?path=' . urlencode($jsonPath) . '&page=' . $page - 1 ?>" class="pagination-button">⬅</a>
        <?php } ?>
        <select onchange="location = this.value;" class="pagination" id="pagination">
            <?php
            $last = 0;
            for ($i = 1; $i <= $totalPages; $i++) {
                $selected = ($i == $page) ? 'selected' : '';
                echo '<option value="?path=' . urlencode($jsonPath) . '&page=' . $i . '" ' . $selected . '>' . $i . '</option>';
                $last++;
            }
            ?>
        </select>
        <?php if ($page != $last) { ?>
            <a id="dont" href="<?= '?path=' . urlencode($jsonPath) . '&page=' . $page + 1 ?>" class="pagination-button">➡</a>
        <?php } ?>
    <?php
    endif;
    echo '</div>';
} else {
    echo 'Please provide a valid JSON path.';
}
?>
<script>
    var lottieElements = document.getElementsByClassName("lottie");
    for (var i = 0; i < lottieElements.length; i++) {
        var lottieElement = lottieElements[i];
        var lottieUrl = lottieElement.getAttribute("data-url");
        var lottieId = "lottie-" + i;
        lottieElement.setAttribute("id", lottieId);
        var animation = bodymovin.loadAnimation({
            container: document.getElementById(lottieId),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: lottieUrl
        });
    }

    var lottie = document.getElementsByClassName("lottie");
    for (var i = 0; i < lottie.length; i++) {
        var lottieElement = lottie[i];
        var lottieUrl = lottieElement.getAttribute("data-url");
        var lottieId = "lottie-" + i;
        lottieElement.setAttribute("id", lottieId);
        var animation = bodymovin.loadAnimation({
            container: document.getElementById(lottieId),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: "/tools/lottie.php?url=" + lottieUrl
        });
    }

    var audio = document.getElementsByClassName("audio");
    for (var i = 0; i < audio.length; i++) {
        var audioElement = audio[i];
        var audioId = "audio-" + i;
        audioElement.setAttribute("id", audioId);
        var audioPlayer = document.getElementById(audioId).getElementsByTagName("audio")[0];
        var audioProgress = document.getElementById("audio-progress-" + i);
        var audioTime = document.getElementById("audio-time-" + i);
        audioPlayer.parentElement.getElementsByClassName("audio-time")[0].innerHTML = "0:00 / 0:00" 
        audioPlayer.addEventListener("timeupdate", function () {
            var progress = audioPlayer.currentTime / audioPlayer.duration;
            audioPlayer.parentElement.getElementsByClassName("audio-progress")[0].getElementsByTagName("div")[0].style.width = progress * 100 + "%";
            var minutes = Math.floor(audioPlayer.currentTime / 60);
            var seconds = Math.floor(audioPlayer.currentTime - minutes * 60);
            var minutesDuration = Math.floor(audioPlayer.duration / 60);
            var secondsDuration = Math.floor(audioPlayer.duration - minutesDuration * 60);
            audioPlayer.parentElement.getElementsByClassName("audio-time")[0].innerHTML = minutes + ":" + (seconds < 10 ? "0" + seconds : seconds) + " / " + minutesDuration + ":" + (secondsDuration < 10 ? "0" + secondsDuration : secondsDuration);
        });
        audioPlayer.parentElement.getElementsByClassName("audion-progress")[0].addEventListener("click", function (e) {
            var x = e.pageX - this.offsetLeft;
            var progress = x / this.offsetWidth;
            audioPlayer.currentTime = progress * audioPlayer.duration;
        });
    }
</script>
<script>
    hljs.highlightAll();
</script>
