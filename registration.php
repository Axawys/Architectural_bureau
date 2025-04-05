<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключение к базе данных
    require_once 'db.php';

    try {
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $FIO = htmlspecialchars($_POST['FIO']);
        $date = htmlspecialchars($_POST['date']);
        $address = htmlspecialchars($_POST['address']);
        $sex = htmlspecialchars($_POST['sex']);
        $interes = htmlspecialchars($_POST['interes']);
        $vk = htmlspecialchars($_POST['vk']);
        $blood = htmlspecialchars($_POST['blood']);
        $resus = htmlspecialchars($_POST['resus']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        // Проверка пароля
        if ($password !== $confirmPassword) {
            header("Location: registration.php?error=passwords_not_matching");
            exit();
        }

        if (strlen($password) <= 6) {
            header("Location: registration.php?error=password_invalid");
            exit();
        }

        //Валидация!!!
        $errors = [];

        if (empty($FIO)) {
            $errors[] = "ФИО обязательно для заполнения.";
        }

        if (empty($date)) {
            $errors[] = "Дата рождения обязательна.";
        }

        if (empty($address)) {
            $errors[] = "Адрес обязателен для заполнения.";
        }

        if (!in_array($sex, ['М', 'Ж'])) {
            $errors[] = "Пол некорректен.";
        }

        if (empty($interes)) {
            $errors[] = "Интересы обязательны для заполнения.";
        }

        if (!empty($vk) && !filter_var($vk, FILTER_VALIDATE_URL)) {
            $errors[] = "Некорректный формат ссылки VK.";
        }

        if (empty($blood)) {
            $errors[] = "Группа крови обязательна.";
        }
        if (!in_array($resus, ['+', '-'])) {
            $errors[] = "Некорректный резус-фактор.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Некорректный email.";
        }

        if (strlen($password) <= 6) {
            $errors[] = "Пароль должен содержать минимум 7 символов.";
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
            exit;
        }



        

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (FIO, date, address, sex, interes, vk, blood, resus, email, password) 
                VALUES (:FIO, :date, :address, :sex, :interes, :vk, :blood, :resus, :email, :password)";

        $stmt = $conn->prepare($sql);

        // Массив с параметрами
        $params = [
            ':FIO' => $FIO,
            ':date' => $date,
            ':address' => $address,
            ':sex' => $sex,
            ':interes' => $interes,
            ':vk' => $vk,
            ':blood' => $blood,
            ':resus' => $resus,
            ':email' => $email,
            ':password' => $hashedPassword,
        ];

        // Привязка параметров
        foreach ($params as $key => $value) {
            $stmt->bindParam($key, $params[$key]);
        }

        if ($stmt->execute()) {
            // Перенаправление на страницу авторизации
            header("Location: autorisation.php");
            exit();
        } else {
            echo "Ошибка регистрации.";
        }
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    }

    // Закрыть соединение
    $conn = null;
}

// Вывод сообщения об ошибке, если пароли не совпадают
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'passwords_not_matching') {
        echo "<div class='alert alert-danger'>Пароли не совпадают. Пожалуйста, попробуйте снова.</div>";
    } elseif ($_GET['error'] === 'password_invalid') {
        echo "<div class='alert alert-danger'>Пароль не соответствует стандарту. Он должен быть длиннее 6 символов, содержать большие и маленькие латинские буквы, цифры, спецсимволы, пробел, дефис и подчеркивание. Русские буквы запрещены.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" type="text/css" />

    <title>Форма регистрации</title>
</head>


<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2>Регистрация</h2>
                <form method="POST" action="">
                    <br>
                    <div class="form-group">
                        <label for="fullName">ФИО</label>
                        <input type="text" class="form-control" id="fullName" name="FIO" placeholder="Введите ФИО" required>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="dob">Дата рождения</label>
                        <input type="date" class="form-control" id="dob" name="date" required>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="address">Адрес</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Введите адрес" required>
                    </div>

                    <br>
                    <div class="form-group">
                        <label>Пол</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="sex" id="male" value="М" required>
                                <label class="form-check-label" for="male">Мужской</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="sex" id="female" value="Ж" required>
                                <label class="form-check-label" for="female">Женский</label>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="interests">Интересы</label>
                        <textarea class="form-control" id="interests" name="interes" rows="3" placeholder="Введите интересы" required></textarea>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="vkProfile">Ссылка на профиль ВК</label>
                        <input type="url" class="form-control" id="vkProfile" name="vk" placeholder="Введите ссылку на профиль ВК" required>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="bloodType">Группа крови</label>
                        <select class="form-control" id="bloodType" name="blood" required>
                            <option value="">Выберите группу крови</option>
                            <option value="I">I (0)</option>
                            <option value="II">II (A)</option>
                            <option value="III">III (B)</option>
                            <option value="IV">IV (AB)</option>
                        </select>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="rhFactor">Резус-фактор</label>
                        <select class="form-control" id="rhFactor" name="resus" required>
                            <option value="">Выберите резус-фактор</option>
                            <option value="+">Положительный (+)</option>
                            <option value="-">Отрицательный (-)</option>
                        </select>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Введите email" required>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Введите пароль" required>
                    </div>

                    <br>
                    <div class="form-group">
                        <label for="confirmPassword">Подтвердите Пароль</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Введите пароль еще раз" required>
                    </div>

                    <br>
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                </form>

                <br /><br />
                <p> Есть аккаунт? <a href="./autorisation.php" class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Войти</a></p>
            </div>
        </div>
    </div>

    <br /><br /><br /><br />
    
    <footer>
    Архитектурное бюро 2025 &copy; Все права защищены
</footer>
</body>
</html>
