<?php
require_once 'logic.php';
session_start();

// === ЗАЩИТА ПАРОЛЕМ ЧЕРЕЗ ПЕРЕМЕННУЮ ОКРУЖЕНИЯ ===
$correct_password = getenv('ADMIN_PASSWORD'); // Получаем пароль из окружения

if ($correct_password === false) {
    error_log("ADMIN_PASSWORD not set in environment");
}

if (isset($_POST['password_check'])) {
    if ($_POST['password'] === $correct_password) {
        $_SESSION['edit_page_access'] = true;
    } else {
        $password_error = 'Неверный пароль!';
    }
}

// Если нет доступа к странице редактирования, показываем форму ввода пароля
if (!isset($_SESSION['edit_page_access']) || $_SESSION['edit_page_access'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Доступ к редактированию</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: url('images/background.jpg') no-repeat center center fixed;
                background-size: cover;
                color: #fff;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .password-container {
                backdrop-filter: blur(10px);
                background-color: rgba(0,0,0,0.7);
                padding: 40px;
                border-radius: 15px;
                max-width: 400px;
                width: 90%;
            }
            .password-container h2 {
                text-align: center;
                margin-bottom: 30px;
            }
            .error-message {
                color: #ff6b6b;
                text-align: center;
                margin-top: 15px;
            }
        </style>
    </head>
    <body>
        <div class="password-container">
            <h2>🔒 Требуется авторизация</h2>
            <form method="POST">
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" 
                           placeholder="Введите пароль" required autofocus>
                </div>
                <button type="submit" name="password_check" class="btn btn-primary w-100">
                    Войти
                </button>
                
                <?php if (isset($password_error)): ?>
                    <div class="error-message"><?= $password_error ?></div>
                <?php endif; ?>
            </form>
            <a href="index.php" class="d-block text-center mt-3 text-white">← На главную</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Проверка авторизации в системе (существующая)
if (!isset($_SESSION['user_id'])) {
    header('Location: autorisation.php');
    exit;
}

// Получаем список должностей из глобальной переменной $job_title
$jobTitles = $job_title; // из logic.php

// Обработка действий
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;
$message = '';
$error = '';

// CREATE - добавление нового сотрудника
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = $_POST['name'];
    $birth = $_POST['birth'];
    $id_type = $_POST['id_type'];
    $description = $_POST['description'];
    $img_path = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'catalogue_images/';
        $original_name = basename($_FILES['image']['name']);
        $img_path = $original_name;
        
        if (file_exists($upload_dir . $original_name)) {
            $img_path = time() . '_' . $original_name;
        }
        
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $img_path);
    }
    
    // ИСПРАВЛЕНО: Добавлен NULL для поля id
    $stmt = $connect->prepare("INSERT INTO employees (name, birth, id_type, description, img_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $birth, $id_type, $description, $img_path]);
    
    $message = "Сотрудник успешно добавлен!";
    $action = 'list';
}

// UPDATE - обновление данных сотрудника
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $birth = $_POST['birth'];
    $id_type = $_POST['id_type'];
    $description = $_POST['description'];
    
    $sql = "UPDATE employees SET name = ?, birth = ?, id_type = ?, description = ?";
    $params = [$name, $birth, $id_type, $description];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Получаем старое фото для удаления
        $stmt = $connect->prepare("SELECT img_path FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $old = $stmt->fetch();
        
        if ($old && $old['img_path'] && file_exists('catalogue_images/' . $old['img_path'])) {
            unlink('catalogue_images/' . $old['img_path']);
        }
        
        $upload_dir = 'catalogue_images/';
        $original_name = basename($_FILES['image']['name']);
        $img_path = $original_name;
        
        if (file_exists($upload_dir . $original_name)) {
            $img_path = time() . '_' . $original_name;
        }
        
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $img_path);
        
        $sql .= ", img_path = ?";
        $params[] = $img_path;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $id;
    
    $stmt = $connect->prepare($sql);
    $stmt->execute($params);
    
    $message = "Данные сотрудника обновлены!";
    $action = 'list';
}

// DELETE - удаление сотрудника
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Удаляем фото
    $stmt = $connect->prepare("SELECT img_path FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    $emp = $stmt->fetch();
    
    if ($emp && $emp['img_path'] && file_exists('catalogue_images/' . $emp['img_path'])) {
        unlink('catalogue_images/' . $emp['img_path']);
    }
    
    // Удаляем запись
    $stmt = $connect->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    
    $message = "Сотрудник удален!";
    header('Location: edit.php?action=list&message=' . urlencode($message));
    exit;
}

// Получаем данные для редактирования
$editEmployee = null;
if ($action === 'edit' && $id) {
    $stmt = $connect->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    $editEmployee = $stmt->fetch();
    
    if (!$editEmployee) {
        $error = "Сотрудник не найден";
        $action = 'list';
    }
}

// Получаем список сотрудников для отображения
$employees = [];
if ($action === 'list') {
    $sql = "SELECT e.*, j.name as job_name 
            FROM employees e 
            LEFT JOIN job_title j ON e.id_type = j.id 
            ORDER BY e.id";
    $stmt = $connect->query($sql);
    $employees = $stmt->fetchAll();
}

// Получаем сообщение из GET параметра
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Управление сотрудниками</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            background: url('images/background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }
        .container-blur {
            backdrop-filter: blur(10px);
            background-color: rgba(0,0,0,0.5);
            padding: 30px;
            border-radius: 10px;
            margin: 20px auto;
        }
        .employee-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            background: rgba(255,255,255,0.1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: #fff;
        }
        .employee-photo {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            object-fit: cover;
            margin: 0 auto;
            display: block;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
        }
        .nav-tabs {
            margin-bottom: 20px;
            border-bottom-color: rgba(255,255,255,0.2);
        }
        .nav-tabs .nav-link {
            color: #fff;
        }
        .nav-tabs .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: #fff;
            border-color: rgba(255,255,255,0.3);
        }
        .current-photo {
            max-width: 200px;
            margin: 10px 0;
            border-radius: 8px;
        }
        .form-label {
            color: #fff;
        }
        .form-control, .form-select {
            background-color: rgba(255,255,255,0.9);
        }
        .table {
            color: #fff;
        }
        .footer-text {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #fff;
        }
        .btn-close {
            filter: invert(1);
        }
        .alert {
            margin-top: 20px;
        }
        .logout-section {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Навигационные вкладки -->
        <div class="container-blur">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link <?= $action === 'list' ? 'active' : '' ?>" href="?action=list">📋 Список сотрудников</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $action === 'add' ? 'active' : '' ?>" href="?action=add">➕ Добавить нового</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">🏠 На главную</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">🚪 Выйти</a>
                </li>
                <li class="nav-item ms-auto">
                    <span class="nav-link text-muted">🔒 Режим редактирования</span>
                </li>
            </ul>
            
            <!-- Сообщения об успехе/ошибке -->
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <!-- Форма добавления -->
            <?php if ($action === 'add'): ?>
                <div class="mt-4">
                    <h3>Добавление нового сотрудника</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ФИО *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Год рождения *</label>
                                <input type="number" name="birth" class="form-control" required min="1900" max="2024">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Должность *</label>
                                <select name="id_type" class="form-control" required>
                                    <option value="">Выберите должность</option>
                                    <?php foreach ($jobTitles as $job): ?>
                                        <option value="<?= $job['id'] ?>"><?= htmlspecialchars($job['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Фото</label>
                                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png">
                                <small class="text-muted">Формат: JPG, PNG. Не обязательно</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Описание *</label>
                                <textarea name="description" class="form-control" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button type="submit" name="add" class="btn btn-primary">💾 Сохранить</button>
                            <a href="?action=list" class="btn btn-secondary">Отмена</a>
                        </div>
                    </form>
                </div>
            
            <!-- Форма редактирования -->
            <?php elseif ($action === 'edit' && $editEmployee): ?>
                <div class="mt-4">
                    <h3>Редактирование: <?= htmlspecialchars($editEmployee['name']) ?></h3>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $editEmployee['id'] ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ФИО *</label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?= htmlspecialchars($editEmployee['name']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Год рождения *</label>
                                <input type="number" name="birth" class="form-control" 
                                       value="<?= $editEmployee['birth'] ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Должность *</label>
                                <select name="id_type" class="form-control" required>
                                    <option value="">Выберите должность</option>
                                    <?php foreach ($jobTitles as $job): ?>
                                        <option value="<?= $job['id'] ?>" 
                                            <?= $job['id'] == $editEmployee['id_type'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($job['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Текущее фото</label>
                                <?php if ($editEmployee['img_path']): ?>
                                    <div>
                                        <img src="catalogue_images/<?= htmlspecialchars($editEmployee['img_path']) ?>" 
                                             class="current-photo" alt="Фото">
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Фото не загружено</p>
                                <?php endif; ?>
                                <label class="form-label mt-2">Загрузить новое фото</label>
                                <input type="file" name="image" class="form-control" accept="image/jpeg,image/png">
                                <small class="text-muted">Оставьте пустым, чтобы не менять фото</small>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Описание *</label>
                                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($editEmployee['description']) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" name="update" class="btn btn-primary">💾 Сохранить изменения</button>
                            <a href="?action=list" class="btn btn-secondary">Отмена</a>
                        </div>
                    </form>
                </div>
            
            <!-- Список сотрудников (READ) -->
            <?php else: ?>
                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Список сотрудников</h3>
                        <a href="?action=add" class="btn btn-success">➕ Добавить сотрудника</a>
                    </div>
                    
                    <?php if (empty($employees)): ?>
                        <div class="alert alert-info">
                            Нет сотрудников. <a href="?action=add">Добавьте первого сотрудника</a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($employees as $emp): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="employee-card">
                                        <div class="text-center mb-3">
                                            <?php if ($emp['img_path']): ?>
                                                <img src="catalogue_images/<?= htmlspecialchars($emp['img_path']) ?>" 
                                                     class="employee-photo" alt="<?= htmlspecialchars($emp['name']) ?>">
                                            <?php else: ?>
                                                <div class="employee-photo bg-secondary d-flex align-items-center justify-content-center">
                                                    <span class="text-white">Нет фото</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h5><?= htmlspecialchars($emp['name']) ?></h5>
                                        <p class="mb-1"><strong>Должность:</strong> <?= htmlspecialchars($emp['job_name'] ?? 'Не указана') ?></p>
                                        <p class="mb-1"><strong>Год рождения:</strong> <?= $emp['birth'] ?></p>
                                        <p class="mb-2"><strong>Описание:</strong><br><?= htmlspecialchars($emp['description']) ?></p>
                                        
                                        <div class="action-buttons">
                                            <a href="?action=edit&id=<?= $emp['id'] ?>" class="btn btn-sm btn-warning">✏️ Редактировать</a>
                                            <a href="?delete=<?= $emp['id'] ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Удалить сотрудника <?= htmlspecialchars($emp['name']) ?>?')">🗑️ Удалить</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer class="footer-text">
        Архитектурное бюро 2026 &copy; Все права защищены
    </footer>
    
    <!-- Bootstrap JS для алертов -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
