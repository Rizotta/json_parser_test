<?php

// Функция подключения к БД
function connect() {
	try {
		$db = new PDO("mysql:host=localhost;dbname=test;charset=UTF8", "user", "password");
	} catch (PDOException $e) {
		die("400 error: " . $e->getMessage() );				// В случае ошибки подключения к БД - выдаём 400 и ошибку
	}

	return $db;
}

// Проверка пользователя, выполнение действий с БД
function check_user( $email ) {

	$pdo = connect();							// Подключение к базе
	$stmt = $pdo->prepare( 'SELECT * from users WHERE email = ?' );		// Подготовка запроса поиска пользователя

	if ( !$stmt->execute([ $email ])) {  					// Что-то пошло не так при выполнении - выдаём 400
		echo = 400;

	} elseif ( $stmt->rowCount() === 0 ) {					// Количество записей = 0, значит пользователь не найден. Создадим

		$stmt = $pdo->prepare( 'INSERT INTO users ( email ) VALUES ( ? )' );	// Подготовка запроса создания пользователя
		$stmt->execute([ $email ]);						// Выполнение запроса

		$result =  $stmt->errorCode();						// Проверяем результат последней операции
		if ( $result == "00000" ) {						// Если результат 00000 - значит всё прошло успешно, выдаём 400
			echo 200;
		} else {								// Если результат не 00000 - значит не успешно, выдаём 400
			echo 400;
		}

	} else {									// Если пользователь уже существует - сообщаем
		echo = 'Такой пользователь есть в БД';
	}
}

$data = json_decode($_POST['jsonData']);
check_user( htmlspecialchars( $data->email ) );
