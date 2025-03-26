<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Handler;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $handler = new Handler();
        $res = $handler->processForm($_POST);
        
        $response = [
            'success' => $res,
        ];

        echo json_encode($response);
    } catch (Exception $e) {
        $err = [
            'success' => false,
            'message' => 'Произошла ошибка: ' . $e->getMessage()
        ];

        echo json_encode($err);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sendsay</title>
    <style>
        body {
            font-family: Roboto, sans-serif;
            max-width: 400px;
            margin: 0 auto;
            padding: 15px;
        }
        .form {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <h3>Регистрация питомца</h3>
    <form id="pet">
        <div class="form">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form">
            <label for="pet_category">Категория питомца:</label>
            <select id="pet_category" name="pet_category" required>
                <option value="">Категория</option>
                <option value="кот">Кот</option>
                <option value="собака">Собака</option>
                <option value="грызун">Грызун</option>
                <option value="рыбки">Рыбки</option>
                <option value="другое">Другое</option>
            </select>
        </div>
        <div class="form">
            <label for="pet_name">Имя питомца:</label>
            <input type="text" id="pet_name" name="pet_name" required>
        </div>
        <button type="submit">Отправить</button>
    </form>

    <script>
        document.getElementById('pet').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log(data)

                if (data.success) {
                    document.getElementById('pet').reset();
                } else {
                    if (data.error) {
                        console.error('Error:', data.error);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
