<?php

// Path to the directory to save the notes in, without trailing slash.
$save_path = 'notes';  // Ensure this matches your directory

// Disable caching.
header('Cache-Control: no-store');

// If no note name is provided, or if the name is too long, or if it contains invalid characters.
if (!isset($_GET['note']) || strlen($_GET['note']) > 64 || !preg_match('/^[a-zA-Z0-9_-]+$/', $_GET['note'])) {

    // Generate a name with 5 random unambiguous characters. Redirect to it.
    header("Location: " . substr(str_shuffle('234579abcdefghjkmnpqrstwxyz'), -5));
    die;
}

$path = $save_path . '/' . $_GET['note'];
date_default_timezone_set('UTC');
$date = date('m/d/Y h:i:s', time());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_text = isset($_POST['new_text']) ? $_POST['new_text'] : '';
    // Append the new content to the existing file.
    if ($new_text) {
        file_put_contents($path, "\n\n === Reply Added $date UTC ===\n\n" . $new_text, FILE_APPEND);
    }
    die;
}

// Get the list of notes from the _tmp directory.
$notes = array_diff(scandir($save_path), array('..', '.'));

//$notes_raw = array_diff(scandir($save_path), array('..', '.'));
//$notes = str_replace('_', ' ', $notes_raw); // replace underscores with spaces

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="...">
    <meta name="author" content="flaskbb Team">
    <link rel="shortcut icon" href="/static/favicon.ico">
    <title>PirateBBS : <?php echo htmlspecialchars($_GET['note'], ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        body {
            margin: 10;
            font-family: Arial, sans-serif;
            font-size: 18px;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
        }
        .wrapper {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .container {
            width: 100%;
            padding: 15px;
            box-sizing: border-box;
            background-color: #ffffff;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
            .container-nav {
            width: 100%;
            height: 50px
            padding: 5px;
            box-sizing: border-box;
            background-color: #ffffff;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .container-main {
            width: 100%;
            height: 500px;
            padding: 15px;
            box-sizing: border-box;
            background-color: #ffffff;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }
        
        .container-reply {
            width: 100%;
            height: 250px;
            padding: 15px;
            box-sizing: border-box;
            background-color: #ffffff;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        textarea {
            resize: none;
            font-family: Arial, sans-serif;
            font-size: 18px;
        }
        
        ul {
            list-style: none;
            background: #ffffff;
        }
        
        ul li {
            display: inline-block;
            position: relative;
        }
        
        ul li a {
            display: block;
            padding: 10px 5px;
            color: #000000;
            text-decoration: none;
            text-align: left;
            font-size: 18px;
        }
        
        ul li ul.dropdown li {
            display: block;
        }
        
        ul li ul.dropdown {
            width: 350px;
            background: #ffffff;
            position: absolute;
            z-index: 999;
            display: none;
        }
        
        ul li a:hover {
            background-color: #ecf0f1;
        }
        
        ul li:hover ul.dropdown {
            display: block;
        }
        
        #editor {
        flex: 0 0 30%;
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
                margin-bottom: 8px;
            }
        }
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #121212;
            }
            .container {
                background-color: #1e1e1e;
                color: #e0e0e0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <div class="container-nav" id="container1">
            <!-- Content for Container 1 -->
            <nav>
                <ul>
                    <li><a href="index.html">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAeCAYAAABe3VzdAAABhGlDQ1BJQ0MgcHJvZmlsZQAAKJF9kT1Iw0AcxV9bxSJVBzsUKZKhOtlFRRxrFYpQIdQKrTqYXPoFTRqSFBdHwbXg4Mdi1cHFWVcHV0EQ/ABxdnBSdJES/5cUWsR4cNyPd/ced+8Af7PKVLMnAaiaZWRSSSGXXxX6XhHEKAYRRURipj4niml4jq97+Ph6F+dZ3uf+HANKwWSATyBOMN2wiDeIZzYtnfM+cZiVJYX4nHjCoAsSP3JddvmNc8lhP88MG9nMPHGYWCh1sdzFrGyoxNPEMUXVKN+fc1nhvMVZrdZZ+578haGCtrLMdZpRpLCIJYgQIKOOCqqwEKdVI8VEhvaTHv4Rxy+SSyZXBYwcC6hBheT4wf/gd7dmcWrSTQolgd4X2/4YA/p2gVbDtr+Pbbt1AgSegSut4681gdlP0hsdLXYEDG0DF9cdTd4DLneAyJMuGZIjBWj6i0Xg/Yy+KQ8M3wL9a25v7X2cPgBZ6ip9AxwcAuMlyl73eHewu7d/z7T7+wGh13K5J6/3qgAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAAN1wAADdcBQiibeAAAAAd0SU1FB+gIHg4dErA+aHwAAAMKSURBVFjDvddriJRVGAfw35opWmluFG7mhUqxrI9agRCxFEGUphBhUK6KgmHUFwMFsaAPFREIFUUlSh8tRVKTUikSgy5WeMGSNcosDIyW7Oa605dn4HB435md2Zl54DDvnPN/z/N/z3M9XUYm16AX8zAD18X8OfTjS3yEH3RY7sJuDKEyjPEZHsPodhO7EfsTxT/hZSzGrbgKEzAb9+IZHEnw3+OBdpGbioFQdDgUdaEbD2MTtmEP3sY63BbvzsV7CdEX20FwBn7FBozC9UHknzrmPY5HE9c4itfbbepl+DsI9GMj7kQPxmEOlsSpDQbu/U4GyTs4i74w82VhxoewIvxvVmBvwAGc6XQkj8Ld+AAXS8x7Ek9ifOA7JtOwb5gppoLTuK9T5OaFeStNjKfbTW4yfm6SXHU83qzyKdiCb/Eh5hdgdo6QXCXS0k0Fez8S1WkHFuaLl+JYttFgBpzfAnLVsTXT/1IBpi/3q7LNTmE1Xknm/sKaKGvdUTXySN6FWyINVRN0dW0Ak/AcfizR+0VeY2t98UEcSv4vKzDRumT9m7BKKj34PcEsraPz01zBCzXA+6N1quBCSVfSneCfKvHzrQnmiTp+eo9M0Vq8iTswFtMjEc8NU1YCNxjmzOXfkudULiTPlyfPn+OrqPW/RJD2Dze6Z4YPbku+8P4C3PJk/UDB+rjM33qj8ZjWqhy4Otn8tzjdqizG+cxMr+GKWL82qzwftyNJT8ycvEr0XA1f+i/qcT6/oF2VZGULcuDeVpPqiRp6NEyzeQTkTkfzuh2LMKZRMrNwe7TwzwahXMkmfN0kwaXJtaES7vEWHoy+sauM2OgsSmuNixF9jZL7JE6uHu58WGl8M751BOvj/jvYIME1cQP8Y5j4N1KCr9YAnsXzuDk79d0NEpwS742NKH43orwMfzhV1lsAGMCq2LBI+hogd6hkj6vD1/8seGd7Dl4SJedUlJo5dQLqyoLk3GyTOhvfZac3tRUpaC1OlJhqKNxjb+7wJdIVd+3J+WQr5ZL4HUqaixHJ/zTh+dXx5E7fAAAAAElFTkSuQmCC" />
                    </a></li>
                    <li><a href="Welcome">BBS Home</a></li>
                    <li>
                       <a href="#">Select Message</a>
                       <ul class="dropdown">
                            <li><a href="Welcome">Welcome</a></li>
                            <?php foreach ($notes as $note): ?>
                            <li><a href="/<?php echo urlencode($note); ?>"><?php echo htmlspecialchars($note, ENT_QUOTES, 'UTF-8'); ?></a></li>
                            <?php endforeach; ?>
                       </ul>
                    </li>
                    <li>
                        <a href="#">Start New Topic</a>
                            <ul class="dropdown">
                                <textarea id="new-subject" placeholder="Enter New Subject (Max 32 characters)" maxlength="32"></textarea>
                            <div id="new-buttons">
                                <button id="submit-new-subject">Submit</button>
                            </div>
                            </ul>
                    </li>

                </ul>
            </nav>
        </div>

        <div class="container-main" id="container2">
            <!-- Content for Container 2 -->
            <div>
                <p>Message Subject: <?php echo htmlspecialchars($_GET['note'], ENT_QUOTES, 'UTF-8'); ?><br><br>
                ===================================================================================</p>
                <?php
                if (is_file($path)) {
                    echo nl2br(htmlspecialchars(file_get_contents($path), ENT_QUOTES, 'UTF-8'));
                }
                ?>
            </div>
        </div>

        <div class="container-reply" id="container3">
            <!-- Content for Container 3 -->

            <div id="editor">
                <div id="buttons">
                    <button id="submit-btn">Submit</button>
                </div>
                <textarea id="new-text" rows="8" placeholder="Enter your comment..."></textarea>
            </div>

        </div>

        <div class="container" id="container4">
            <!-- Content for Container 4 -->
            <p>Footer</p>
        </div>
    </div>
        <script>
        document.getElementById('submit-btn').addEventListener('click', function() {
            var newText = document.getElementById('new-text').value;
            if (newText.trim() !== "") {
                var request = new XMLHttpRequest();
                request.open('POST', window.location.href, true);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                request.send('new_text=' + encodeURIComponent(newText));
                document.getElementById('new-text').value = '';
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            }
        });
        
        document.getElementById('submit-new-subject').addEventListener('click', function() {
            var newSubject = document.getElementById('new-subject').value.trim();
            if (newSubject !== "") {
            // Replace spaces with underscores and encode the subject
            newSubject = encodeURIComponent(newSubject.replace(/\s+/g, '_'));
            // Redirect to the new subject URL
            window.location.href = newSubject;
        } else {
            alert("Please enter a valid subject.");
            }
        });


        // Auto-refresh the current content every second
        setInterval(function() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', window.location.href, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(xhr.responseText, 'text/html');
                    var currentContent = doc.getElementById('current-content').innerHTML;
                    document.getElementById('current-content').innerHTML = currentContent;
                }
            };
            xhr.send();
        }, 1000);
    </script>


</body>
</html>
