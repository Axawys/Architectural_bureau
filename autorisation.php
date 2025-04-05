<?php
session_start(); // Начинаем сессию

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключение к базе данных
    require_once 'db.php';

    try {
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        // Получаем пользователя по email
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Проверяем, совпадает ли введённый пароль
        if ($user && password_verify($password, $user['password'])) {
            // Устанавливаем сессию
            $_SESSION['user_id'] = $user['id']; // Предположим, что у Вас есть id пользователя в таблице
            $_SESSION['email'] = $user['email'];
            header("Location: index.php"); // Перенаправление на личный кабинет или другую страницу
            exit();
        } else {
            header("Location: autorisation.php?error=passwords_not_matching");
        }
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    }

    // Закрыть соединение
    $conn = null;
}

if (isset($_GET['error']) && $_GET['error'] === 'passwords_not_matching') {
    echo "<div class='alert alert-danger'>Неправильный логин или пароль</div>";                                          
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <title>Авторизация</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h2>Авторизация</h2>
                <form method="POST">
                    <br />
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Введите email" required>
                    </div>

                    <br />
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Введите пароль" required>
                    </div>
                    <br />
                    <button type="submit" class="btn btn-primary">Войти</button>
                </form>

                <br /><br />
                <p> Нет аккаунта? <a href="./registration.php" class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Регистрация</a></p>
            </div>
        </div>
    </div>
    <footer>
    Архитектурное бюро 2025 &copy; Все права защищены
</footer>
</body>
</html>