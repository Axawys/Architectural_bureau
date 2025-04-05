<?php
include "logic.php";

// Авторизация
session_start(); // Начало сессии

if (!isset($_SESSION['user_id'])) {
    // Пользователь не авторизован, показываем сообщение
    echo '<br><br><div class="container">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <h3>Вы не вошли в аккаунт</h3>
                    <p>
                        <a href="./autorisation.php" class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Войти в аккаунт</a> или 
                        <a href="./registration.php" class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Регистрация</a>
                    </p>
                </div>
            </div>
        </div>';
} else {
    echo '
        <br><br>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <h4>Главное меню</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <p><a href="logout.php" class="btn btn-danger">Выйти из аккаунта</a></p>
                    </div>
                </div>
            </div>
        </div>

        <br><br><br><br>
        <div class="container text-center">
            <h4>Фильтры</h4>
            <br><br>
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        <form method="get" action="./">
                            <p class="text-center">По году рождения:</p>
                            <input type="text" class="form-control" name="birth" placeholder="Введите год рождения" value="' . htmlspecialchars($price_filter) . '">
                            <br><br><br>

                            <p class="text-center">По ФИО:</p>
                            <input type="text" class="form-control" name="name" placeholder="Введите название" value="' . htmlspecialchars($name_filter) . '">
                            <br><br><br>

                            <p class="text-center">По личной характеристике:</p>
                            <input type="text" class="form-control" name="description" placeholder="Введите личную характеристику" value="' . htmlspecialchars($description_filter) . '">
                            <br><br><br>
                    
                            <p class="text-center">По должности:</p>
                            <select name="id_type" class="form-select">
                                <option value="" ' . ($type_filter == '' ? 'selected' : '') . '>Все</option>
                                <option value="1" ' . ($type_filter == '1' ? 'selected' : '') . '>Архитектор</option>
                                <option value="2" ' . ($type_filter == '2' ? 'selected' : '') . '>Инженер</option>
                                <option value="3" ' . ($type_filter == '3' ? 'selected' : '') . '>Дизайнер</option>
                                <option value="4" ' . ($type_filter == '4' ? 'selected' : '') . '>Генеральный директор</option>
                                <option value="5" ' . ($type_filter == '5' ? 'selected' : '') . '>Старший архитектор</option>
                                <option value="6" ' . ($type_filter == '6' ? 'selected' : '') . '>Директор по операциям</option>
                                <option value="7" ' . ($type_filter == '7' ? 'selected' : '') . '>Председатель совета директоров</option>
                                <option value="8" ' . ($type_filter == '8' ? 'selected' : '') . '>Менеджер проектов</option>
                            </select>
                            <br><br><br>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Применить фильтр</button>
                                <a href="./" class="btn btn-danger">Очистить</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br><br><br><br><br>

        <div class="container text-center">
            <h4>Сотрудники</h4>
            <br>
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th scope="col">Фото</th>
                        <th scope="col">ФИО</th>
                        <th scope="col">Год рождения</th>
                        <th scope="col">Должность</th>
                        <th scope="col">Личная характеристика</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($employees as $employee) { // Исправлено $emloyee на $employee
        echo '<tr>
                <td>';
        if (isset($_SESSION['user_id'])) {
            echo '<img class="img-thumbnail" alt="Responsive image" src="catalogue_images/' . htmlspecialchars($employee['img_path']) . '"/>';
        } else {
            echo '<img class="img-thumbnail" alt="Responsive image" src="catalogue_images/low.png"/>';
        }
        echo '</td>
                <td>' . htmlspecialchars($employee['name']) . '</td>
                <td>' . htmlspecialchars($employee['birth']) . '</td>
                <td>' . htmlspecialchars($job_title[$employee['id_type'] - 1]['name']) . '</td>
                <td>' . htmlspecialchars($employee['description']) . '</td>
              </tr>';
    }

    echo '</tbody>
            </table>
        </div>
    ';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <title>Архитектурное бюро</title>
</head>
<body>
    <br><br><br><br>
    <footer>
    Архитектурное бюро 2025 &copy; Все права защищены
</footer>

</body>
</html>