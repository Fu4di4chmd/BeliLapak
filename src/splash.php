<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BeliLapak</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            width: 100%;
            height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background: #ffffff;
            overflow: hidden;
        }

        .splash-container {
            text-align: center;
        }

        .splash-logo {
            width: 280px;
            max-width: 80%;
            height: auto;

            animation: logoAnimation 1.5s ease;
        }

        .tagline {
            margin-top: 15px;

            font-size: 14px;
            color: #777;

            opacity: 0;

            animation: showTagline 0.8s ease forwards;
            animation-delay: 0.8s;
        }

        .loader {
            width: 60px;
            height: 4px;

            margin: 25px auto 0;

            background: #eeeeee;
            border-radius: 20px;

            overflow: hidden;
            position: relative;
        }

        .loader::before {
            content: "";

            position: absolute;
            top: 0;
            left: -100%;

            width: 100%;
            height: 100%;

            background: #e91e63;

            animation: loading 1.9s infinite;
        }

        @keyframes logoAnimation {

            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            60% {
                transform: scale(1.08);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }

        }

        @keyframes showTagline {

            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }

        }

        @keyframes loading {

            from {
                left: -100%;
            }

            to {
                left: 100%;
            }

        }
    </style>

</head>

<body>

    <div class="splash-container">

        <img
            src="/BeliLapak/src/static/uploads/uploads/fotooooo.jpeg"
            alt="Logo BeliLapak"
            class="splash-logo"
        >

        <div class="tagline">
            Belanja mudah, pilihan favoritmu.
        </div>

        <div class="loader"></div>

    </div>

    <script>
        setTimeout(function () {

            window.location.href =
                "/BeliLapak/src/index.php?action=katalog";

        }, 2500);
    </script>

</body>

</html>