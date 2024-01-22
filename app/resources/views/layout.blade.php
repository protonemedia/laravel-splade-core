<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Splade Core</title>
        @vite("resources/js/app.js")
    </head>
    <body>
        <div id="app">
            <h1>Splade Core demo app</h1>

            @yield('content')
        </div>
    </body>
</html>
