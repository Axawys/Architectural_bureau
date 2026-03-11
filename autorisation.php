<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'db.php';
    try {
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.php");
            exit();
        } else {
            $errorMessage = "Неправильный логин или пароль";
        }
    } catch (PDOException $e) {
        $errorMessage = "Ошибка подключения: " . $e->getMessage();
    }
    $connect = null;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet">
<title>Авторизация</title>
<style>
body {
    background: url('images/background.jpg') no-repeat center center fixed;
    background-size: cover;
}
.form-container {
    backdrop-filter: blur(10px);
    background-color: rgba(255,255,255,0.3);
    padding: 30px;
    border-radius: 10px;
    max-width: 400px;
    margin: 80px auto;
}
footer {
    text-align: center;
    margin-top: 50px;
    color: #fff;
}
</style>
</head>
<body>
<div class="form-container">
    <h2 class="mb-4 text-center">Авторизация</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Введите email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Введите пароль" required>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Войти</button>
            <button type="button" class="btn btn-secondary" onclick="goHome()">Главная</button>
        </div>
    </form>

    <p class="mt-3 text-center">
        Нет аккаунта? 
        <a href="./registration.php" class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Регистрация</a>
    </p>
</div>

<script>
function goHome() {
    if(confirm("Все введённые данные будут потеряны. Вы уверены, что хотите вернуться на главную?")) {
        window.location.href = 'index.php';
    }
}
</script>

<footer>
    Архитектурное бюро 2025 &copy; Все права защищены
</footer>
</body>
</html>
