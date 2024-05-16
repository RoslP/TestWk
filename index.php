<?php require 'DB/Calls.php' ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Unlimited nested structure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--    подключение к кастомным иконкам font awesome-->
    <script src="https://kit.fontawesome.com/7329f6dda8.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <!--    скрипты необходимые для карусели и других элементов bootstrap-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
            crossorigin="anonymous"></script>
    <!--    подключение стрилей-->
    <!--    js-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/Assets/JS/Process.js"></script>
    <!--    подключение шрифтов от google fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Assets/css/style.css">
</head>
<body class="body">
<header>
    <div class="container">
        <div class="row">
            <div class="col-2"><h1>Тестовое задание</h1></div>
            <div class="col-10"></div>
        </div>
    </div>
</header>
<div class="container">
    <div class="row">
        <div class="Export col-6">
        <label id="data-load"></label>
        </div>
        <div class="Shliapa col-6">
            <div class="row">
                <div class="Elll col-6">
                    <label>Заполнить данными из categories.json таблицу header в БД</label>
                    <div class="w-100"></div>
                    <button id="write-data-from-categories" type="button">Заполнить</button>
                </div>
                <div class="Elll col-6">
                    <label>Удалить все данные из бд</label>
                    <div class="w-100"></div>
                    <br>
                    <button id="delete-data-from-categories" type="button">Удалить</button>
                </div>
                <div class="Elll col-6">
                    <label>Записать данные в TXT файл</label>
                    <div class="w-100"></div>
                    <button id="put-data-in-txt" type="button">Записать</button>
                </div>
                <div class="w-100"></div>
                <div class="Elll col-6">
                    <label>Вывести данные</label>
                    <div class="w-100"></div>
                    <button id="read-data-from-txt" type="button">Вывести</button>
                </div>
                <div class="w-100"></div>
                <div class="Elll col-6">
                    <label>Вывести только названия до 1 уровня вложенности</label>
                    <div class="w-100"></div>
                    <button id="read-data-from-txt-semi" type="button">Вывести</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>