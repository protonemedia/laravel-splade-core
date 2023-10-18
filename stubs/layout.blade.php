<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My app</title>
        @vite("resources/js/app.js")
    </head>
    <body>
        <script> @stack('splade-templates') </script>

        <div id="app">
            {{ $slot }}
        </div>
    </body>
</html>