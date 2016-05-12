<!DOCTYPE html>
<html>
    <head>
        <title>Please add an Environment</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100,300italic" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
            }

            .subtitle {
                margin-top: 0.5em;
                font-size: 48px;
            }

            .command {
            	font-weight: 100;
            	font-style: italic;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Please add an Environment File (<span class="command">.env</span>) to the root directory.</div>
                <div class="subtitle">Either copy the <span class="command">.env.example</span> file, or refer to the <a class="command" target="_blank" href="https://laravel.com/docs/master/configuration#environment-configuration">docs</a>.
            </div>
        </div>
    </body>
</html>