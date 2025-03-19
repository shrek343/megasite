<?php
// Подключаем файл с соединением к базе данных
include 'connect.php'; 

// Переменная для сообщения об успешной регистрации
$registration_success = false;
$error_message = '';

// Проверяем, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы и экранируем их
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $password = $_POST['password'];

    // Проверяем, существует ли уже пользователь с таким email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "Пользователь с таким email уже существует.";
    } else {
        // Хешируем пароль
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Подготовка и выполнение SQL-запроса
        $stmt = $conn->prepare("INSERT INTO users (name, surname, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $surname, $email, $hashed_password);

        // Проверяем, успешно ли выполнен запрос
        if ($stmt->execute()) {
            // Успешная регистрация, перенаправляем на страницу входа
            header("Location: login.php?registration=success");
            exit(); // Завершаем выполнение скрипта
        } else {
            $error_message = "Ошибка при регистрации: " . $stmt->error;
        }
    }

    // Закрываем подготовленный запрос
    $stmt->close();
}

// Закрываем соединение с базой данных
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .navbar {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .registration-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 50px auto; /* Центрирование формы */
        }
        h2 {
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: calc(100% - 16px); /* Учитываем отступы */
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Учитываем отступы в ширине */
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border: 1px solid #d6e9c6;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
        }
        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            border: 1px solid #ebccd1;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
        }
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Главная</a>
        <a href="auth.php">Авторизация</a>
    </div>
    <div class="registration-form">
        <h2>Регистрация</h2>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="name">Имя:</label>
            <input type="text" name="name" required>

            <label for="surname">Фамилия:</label>
            <input type="text" name="surname" required>

            <label for="password">Пароль:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Зарегистрироваться">
        </form>
        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        <p class="login-link">Если у вас уже есть аккаунт, <a href="auth.php">авторизуйтесь</a>.</p>
    </div>
</body>
</html>