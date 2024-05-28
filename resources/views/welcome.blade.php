<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Stay my trip backend</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            width: 100vw;
            background: #212020;
            color: #e5dddd;
            font-size: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        body a {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: rgba(86, 81, 81, 0.6);
            border-radius: 10px;
            padding: 10px;
            color: #e5dddd;
            text-decoration: none;
            transition: 0.3s;
        }

        body a:hover {
            background: rgba(86, 81, 81, 0.8);
            color: #fff;
        }
    </style>
</head>
<body class="body">

<a href="#">Welcome to stay my trip</a>
</body>
</html>
