<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Room Management' ?></title>
    <link rel="stylesheet" href="/LuneraHotel/App/src/output.css">
    <link rel="stylesheet" href="/LuneraHotel/App/Public/CSS/fontawesome.min.css">
    <link rel="stylesheet" href="/LuneraHotel/App/Public/CSS/all.min.css">
    <link rel="stylesheet" href="/LuneraHotel/App/Public/CSS/login.css">
    <link rel="stylesheet" href="/LuneraHotel/App/Public/CSS/dashboard.css">
    <link rel="icon" type="image/jpg" href="/LuneraHotel/App/Public/images/logo.jpg">
    <!--npx @tailwindcss/cli -i ./src/input.css -o ./src/output.css --watch
    cd C:\ngrok
    ngrok http 80
    -->
</head>
<body class=" bg-[#f8f8f8] font-mono overflow-x-hidden">
    <?= $content  ?>
</body>

</html>

