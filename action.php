<?php

// Функция подключения к БД

// Функция подключения к БД
function connect() {
	try {
		$db = new PDO("mysql:host=localhost;dbname=test;charset=UTF8", "mysql", "mysql");
	} catch (PDOException $e) {
		die("Ошибка подключения к базе. ");	// В случае ошибки подключения к БД - выдаём 400 и ошибку
	}

	return $db;
}

// Проверка пользователя, выполнение действий с БД
function check_user( $email ) {

	$pdo = connect();							// Подключение к базе
	$stmt = $pdo->prepare( 'SELECT * from users WHERE email = ?' );		// Подготовка запроса поиска пользователя

	if ( !$stmt->execute([ $email ])) {  					// Что-то пошло не так при выполнении - выдаём 400
		header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
		echo "Возникла ошибка.";

	} elseif ( $stmt->rowCount() === 0 ) {					// Количество записей = 0, значит пользователь не найден. Создадим

		$stmt = $pdo->prepare( 'INSERT INTO users ( email ) VALUES ( ? )' );	// Подготовка запроса создания пользователя
		$stmt->execute([ $email ]);						// Выполнение запроса

		$result =  $stmt->errorCode();						// Проверяем результат последней операции
		if ( $result == "00000" ) {						// Если результат 00000 - значит всё прошло успешно, выдаём 400
			header($_SERVER["SERVER_PROTOCOL"]." 200 ОК");
			echo "Пользователь успешно добавлен";
		} else {								// Если результат не 00000 - значит не успешно, выдаём 400
			header($_SERVER["SERVER_PROTOCOL"]." 400 Bad Request");
			echo "Возникла ошибка.";
		}

	} else {									// Если пользователь уже существует - сообщаем
		header($_SERVER["SERVER_PROTOCOL"]." 200 ОК");
		echo 'Такой пользователь уже существует.';
	}
}

$data = json_decode($_POST['jsonData']);
check_user( htmlentities( $data->email , ENT_COMPAT, 'UTF-8' ) );
