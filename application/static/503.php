<?php
http_response_code(503);
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Service Temporarily Unavailable</title>
    <meta name="robots" content="noindex, nofollow" />

    <style type="text/css">

        html {
            height: 100%;
            font-size: 18px;
        }

        body {
            min-height: 100%;
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-flex-flow: column nowrap;
            -ms-flex-flow: column nowrap;
            flex-flow: column nowrap;
            -webkit-box-pack: center;
            -webkit-justify-content: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            margin: 0;
            font: normal 1em/1.5 helvetica, roboto, arial, sans-serif;
            text-align: center;
            color: #444;
        }

        h1 {
            font-weight: normal;
            line-height: 1.1;
            color: #888;
        }

        a {
            color: #888;
        }

        .container {
            max-width: 30em;
            margin: 3em auto;
            padding: 0 4%;
        }

    </style>
</head>
<body>

<div class="container">
    <h1>Service Temporarily Unavailable</h1>
    <p>Thank you for your understanding.</p>
</div>

</body>
</html>
