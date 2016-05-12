<!DOCTYPE html>
<html>
    <head>
        <title>Please generate a Key</title>

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

            .wrapper {
                display: flex;
                align-items: center;
                height: 100%;
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

            .command {
            	font-weight: 100;
            	font-style: italic;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="container">
                <div class="content">
                    <div class="title">Please run <span class="command">php artisan key:generate</span> on the Command Line.</div>
                </div>
            </div>
        </div>
    </body>
</html>