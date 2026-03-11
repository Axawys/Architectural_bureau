<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'db.php';
    try {
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

        $errors = [];
        if(empty($FIO)) $errors[]="ФИО обязательно.";
        if(empty($date)) $errors[]="Дата рождения обязательна.";
        if(empty($address)) $errors[]="Адрес обязателен.";
        if(!in_array($sex,['М','Ж'])) $errors[]="Пол некорректен.";
        if(empty($interes)) $errors[]="Интересы обязательны.";
        if(!empty($vk) && !filter_var($vk,FILTER_VALIDATE_URL)) $errors[]="Некорректная ссылка VK.";
        if(empty($blood)) $errors[]="Группа крови обязательна.";
        if(!in_array($resus,['+','-'])) $errors[]="Резус-фактор некорректен.";
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]="Некорректный email.";
        
        // Проверка на существующий email
        $checkEmail = $connect->prepare("SELECT id FROM users WHERE email = :email");
        $checkEmail->execute([':email' => $email]);
        if($checkEmail->rowCount() > 0) {
            $errors[]="Эта почта уже используется";
        }
        
        if(strlen($password)<=6) $errors[]="Пароль минимум 7 символов.";
        if($password!==$confirmPassword) $errors[]="Пароли не совпадают.";

        if(!empty($errors)){
            foreach($errors as $e) echo "<div class='alert alert-danger'>$e</div>";
            exit;
        }

        $hashedPassword=password_hash($password,PASSWORD_DEFAULT);

        $sql="INSERT INTO users (FIO,date,address,sex,interes,vk,blood,resus,email,password)
        VALUES (:FIO,:date,:address,:sex,:interes,:vk,:blood,:resus,:email,:password)";
        $stmt=$connect->prepare($sql);
        $params=[
            ':FIO'=>$FIO,':date'=>$date,':address'=>$address,':sex'=>$sex,
            ':interes'=>$interes,':vk'=>$vk,':blood'=>$blood,':resus'=>$resus,
            ':email'=>$email,':password'=>$hashedPassword
        ];
        foreach($params as $k=>$v) $stmt->bindParam($k,$params[$k]);
        if($stmt->execute()) header("Location: autorisation.php");
    } catch(PDOException $e){ echo "Ошибка подключения: ".$e->getMessage(); }
    $connect=null;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/bootstrap.min.css" rel="stylesheet">
<title>Регистрация</title>
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
    max-width: 600px;
    margin: 50px auto;
}
.is-invalid { border-color: #dc3545; }
.text-danger { font-size: 0.85em; }
</style>
</head>
<body>
<div class="form-container">
<h2>Регистрация</h2>
<form method="POST" id="regForm">
    <div class="form-group">
        <label>ФИО</label>
        <input type="text" class="form-control" name="FIO" id="FIO" required>
        <small class="text-danger d-none">Введите ФИО</small>
    </div>

    <div class="form-group mt-3">
        <label>Дата рождения</label>
        <div class="d-flex gap-2">
            <select id="day" class="form-control" required></select>
            <select id="month" class="form-control" required></select>
            <select id="year" class="form-control" required></select>
        </div>
        <small id="dobError" class="text-danger d-none">Выберите корректную дату</small>
        <input type="hidden" id="dob" name="date">
    </div>

    <div class="form-group mt-3">
        <label>Адрес</label>
        <input type="text" class="form-control" name="address" id="address" required>
        <small class="text-danger d-none">Введите адрес</small>
    </div>

    <div class="form-group mt-3">
        <label>Пол</label>
        <select class="form-control" name="sex" id="sex" required>
            <option value="">Выберите пол</option>
            <option value="М">Мужской</option>
            <option value="Ж">Женский</option>
        </select>
        <small class="text-danger d-none" id="sexError">Выберите пол</small>
    </div>

    <div class="form-group mt-3">
        <label>Интересы</label>
        <textarea class="form-control" name="interes" id="interes" rows="3" required></textarea>
        <small class="text-danger d-none">Введите интересы</small>
    </div>

    <div class="form-group mt-3">
        <label>Ссылка VK</label>
        <input type="url" class="form-control" name="vk" id="vk" required>
        <small class="text-danger d-none">Введите корректную ссылку</small>
    </div>

    <div class="form-group mt-3">
        <label>Группа крови</label>
        <select class="form-control" name="blood" id="blood" required>
            <option value="">Выберите группу</option>
            <option value="I">I (0)</option>
            <option value="II">II (A)</option>
            <option value="III">III (B)</option>
            <option value="IV">IV (AB)</option>
        </select>
        <small class="text-danger d-none" id="bloodError">Выберите группу крови</small>
    </div>

    <div class="form-group mt-3">
        <label>Резус-фактор</label>
        <select class="form-control" name="resus" id="resus" required>
            <option value="">Выберите</option>
            <option value="+">Положительный (+)</option>
            <option value="-">Отрицательный (-)</option>
        </select>
        <small class="text-danger d-none" id="resusError">Выберите резус-фактор</small>
    </div>

    <div class="form-group mt-3">
        <label>Email</label>
        <input type="email" class="form-control" name="email" id="email" required>
        <small class="text-danger d-none" id="emailFormatError">Введите корректный email</small>
        <small class="text-danger d-none" id="emailError"></small>
    </div>

    <div class="form-group mt-3">
        <label>Пароль</label>
        <input type="password" class="form-control" name="password" id="password" required>
        <small class="text-danger d-none">Пароль минимум 7 символов</small>
    </div>

    <div class="form-group mt-3">
        <label>Подтвердите пароль</label>
        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" required>
        <small class="text-danger d-none">Пароли должны совпадать</small>
    </div>

    
    <div class="mt-3 d-flex justify-content-between">
        <button type="submit" class="btn btn-primary" id="submitBtn">Зарегистрироваться</button>
        <button type="button" class="btn btn-secondary" onclick="goHome()">Вернуться в главное меню</button>
    </div>

    <script>
    function goHome() {
        if(confirm("Все заполненные данные будут потеряны. Вы уверены, что хотите вернуться?")) {
            window.location.href = 'index.php';
        }
    }
    </script>
</form>

</div>

<script>
const day = document.getElementById('day');
const month = document.getElementById('month');
const year = document.getElementById('year');
const dobInput = document.getElementById('dob');
const dobError = document.getElementById('dobError');
const blood = document.getElementById('blood');
const bloodError = document.getElementById('bloodError');
const resus = document.getElementById('resus');
const resusError = document.getElementById('resusError');
const sex = document.getElementById('sex');
const sexError = document.getElementById('sexError');
const submitBtn = document.getElementById('submitBtn');
const emailFormatError = document.getElementById('emailFormatError');
const emailError = document.getElementById('emailError');
const emailInput = document.getElementById('email');

// Дата рождения
function populateMonths() {
    month.innerHTML = '<option value="">Месяц</option>';
    for(let m=1;m<=12;m++){
        const opt = document.createElement('option'); 
        opt.value = opt.text = m<10?'0'+m:m;
        month.appendChild(opt);
    }
}

function populateYears() {
    year.innerHTML = '<option value="">Год</option>';
    const thisYear = new Date().getFullYear();
    for(let y = thisYear; y>=1900; y--){
        const opt = document.createElement('option'); 
        opt.value = opt.text = y;
        year.appendChild(opt);
    }
}

function updateDays() {
    const m = parseInt(month.value), y = parseInt(year.value);
    let selectedDay = day.value;
    let daysInMonth = (m && y) ? new Date(y,m,0).getDate() : 31;
    day.innerHTML = '<option value="">День</option>';
    for(let i=1;i<=daysInMonth;i++){
        const opt = document.createElement('option'); 
        opt.value = opt.text = i<10?'0'+i:i;
        day.appendChild(opt);
    }
    if(selectedDay && selectedDay<=daysInMonth) day.value = selectedDay;
}

function validateDob() {
    const d = day.value, m = month.value, y = year.value;
    const valid = d && m && y;
    dobInput.value = valid ? `${d}/${m}/${y}` : '';
    dobError.classList.toggle('d-none', valid);
    return valid;
}

function validateSelect(sel,error) {
    const valid = sel.value !== '';
    error.classList.toggle('d-none', valid);
    sel.classList.toggle('is-invalid', !valid);
    return valid;
}

function validateField(id, validator) {
    const el = document.getElementById(id);
    const val = el.value.trim();
    const valid = validator(val);
    el.classList.toggle('is-invalid', !valid);
    if (el.nextElementSibling && id !== 'email') {
        el.nextElementSibling.classList.toggle('d-none', valid);
    }
    return valid;
}

// Проверка уникальности email
async function checkEmailUnique() {
    const email = emailInput.value.trim();
    
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        return false;
    }
    
    try {
        const response = await fetch('check_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        });
        
        const data = await response.json();
        
        if (data.exists) {
            emailError.textContent = 'эта почта уже используется';
            emailError.classList.remove('d-none');
            emailFormatError.classList.add('d-none');
            emailInput.classList.add('is-invalid');
            return false;
        } else {
            emailError.classList.add('d-none');
            if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                emailFormatError.classList.add('d-none');
                emailInput.classList.remove('is-invalid');
            }
            return true;
        }
    } catch (error) {
        console.error('Ошибка при проверке email:', error);
        return true;
    }
}

async function validateForm() {
    const validDob = validateDob();
    const validFIO = validateField('FIO', v=>v.length>0);
    const validAddress = validateField('address', v=>v.length>0);
    const validInteres = validateField('interes', v=>v.length>0);
    const validVK = validateField('vk', v=>/^https?:\/\/.+$/.test(v));
    
    // Валидация email формата
    const email = emailInput.value.trim();
    const validEmailFormat = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    emailInput.classList.toggle('is-invalid', !validEmailFormat);
    emailFormatError.classList.toggle('d-none', validEmailFormat);
    
    const validPassword = validateField('password', v=>v.length>=7);
    const validConfirm = validateField('confirmPassword', v=>v===document.getElementById('password').value);
    const validBlood = validateSelect(blood, bloodError);
    const validResus = validateSelect(resus, resusError);
    const validSex = validateSelect(sex, sexError);
    
    // Проверка уникальности email
    let validEmailUnique = true;
    if (validEmailFormat) {
        validEmailUnique = await checkEmailUnique();
    }
    
    submitBtn.disabled = !(validDob && validFIO && validAddress && validInteres &&
                           validVK && validEmailFormat && validPassword && validConfirm &&
                           validBlood && validResus && validSex && validEmailUnique);
}

// Инициализация
populateMonths(); 
populateYears(); 
updateDays();

// Слушатели изменений
month.addEventListener('change', ()=>{ updateDays(); validateForm(); });
year.addEventListener('change', ()=>{ updateDays(); validateForm(); });
day.addEventListener('change', validateForm);
blood.addEventListener('change', validateForm);
resus.addEventListener('change', validateForm);
sex.addEventListener('change', validateForm);

// Слушатели для полей ввода
document.getElementById('regForm').addEventListener('input', validateForm);

// Специальный обработчик для email
emailInput.addEventListener('blur', async function() {
    await checkEmailUnique();
    validateForm();
});

emailInput.addEventListener('input', function() {
    // Скрываем ошибку уникальности при изменении email
    emailError.classList.add('d-none');
    validateForm();
});

// Запускаем валидацию при загрузке
validateForm();
</script>
</body>
</html>
