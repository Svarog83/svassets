<?php /** @noinspection PhpDeprecationInspection */
use function BenTools\StringCombinations\string_combinations;

$word = !empty( $_POST['searchWord'] ) ? $_POST['searchWord'] : '';

if ($word) {
	require_once '../vendor/autoload.php';

	$started = time();
	$maxParams = 20000;
	$length = mb_strlen($word);
	$allOK = true;
	if ($length < 4) {
		echo 'Длина строки должна быть больше 3 символов. <br>';
		$allOK = false;
	}

	if ($length > 8) {
		echo 'Длина строки не может быть больше 8 символов.<br>';
		$allOK = false;
	}

	$foundWords   = [];
	if ($allOK) {
		$db_host_name_main = 'localhost';
		$db_name_main      = 'stocks';
		$db_user_name_main = 'googleUser';
		$db_password_main  = 'RNCX2ffBwStq';

		$connect_main = mysqli_connect($db_host_name_main, $db_user_name_main, $db_password_main);
		mysqli_select_db($connect_main, $db_name_main);

		$query = "SET NAMES 'utf8'";
		$result = mysqli_query($connect_main, $query) or die(__FILE__ . __LINE__);

		$query = 'SELECT COUNT(*) FROM nouns2';
		$result = mysqli_query($connect_main, $query) or die(__FILE__ . __LINE__);
		$row = mysqli_fetch_row($result);
		$tableWasEmpty = false;
		if (!$row || empty($row[0])) {
			$tableWasEmpty = true;
			//if memory table is empty - we need to populate it with data
			$query = 'INSERT INTO nouns2 SELECT * FROM nouns';
			$result = mysqli_query($connect_main, $query) or die(__FILE__ . __LINE__);
		}

		//$combinations = string_combinations('виноград', $min = 2, $max=7);
		//$combinations = string_combinations('молоко', $min = 2, $max=6);

		$combinations = string_combinations($word, $min = 2, $length);
		//dump(count($combinations));
		$combinations = $combinations->withoutDuplicates();
		$i            = 0;
		$params       = [];
		foreach ($combinations as $c => $combination) {
			if ($i < $maxParams) {
				$params[] = $combination;
				$i++;
			} else {
				$i = 0;

				$query = /** @lang SQL */
					"SELECT word FROM nouns2 WHERE word IN('" . implode("', '", $params) . "')";

				$result = mysqli_query($connect_main, $query) or die(__FILE__ . __LINE__);


				while ($row = mysqli_fetch_row($result)) {
					$foundWords[] = $row[0];
				}
			}
		}

		if (count($params)) {
			$query = /** @lang SQL */
				"SELECT word FROM nouns2 WHERE word IN('" . implode("', '", $params) . "')";

			$result = mysqli_query($connect_main, $query) or die(__FILE__ . __LINE__);


			while ($row = mysqli_fetch_row($result)) {
				$foundWords[] = $row[0];
			}
		}
		/*foreach (string_combinations('вино', $min = 2) as $combination) { // Can also be string_combinations(['a', 'b', 'c'])
		echo $combination . PHP_EOL;
		}*/
	}

	if (count($foundWords)) {
		$foundWords = array_unique($foundWords);
		if ($tableWasEmpty) {
			echo 'Таблица nouns2 была пустая, пришлось ее заполнить<br>';
		}
		echo 'Из букв слова `'.$word.'` найдено слов: <b>'. count($foundWords).'</b><br>';
		echo implode ('<br>', $foundWords) . '<br>';
		echo 'Прошло: ' . (time() - $started) .' секунд. <br>';
		echo sprintf(
			'Memory usage: %sMB / Peak usage: %sMB',
			round(memory_get_usage(true) / 1024 / 1024),
			round(memory_get_peak_usage(true) / 1024 / 1024)
		) . '<br><br>';
	}
	else if ($allOK) {
		echo 'Слов не найдено.<br>';
	}

	echo '<a href="words.php">Вернуться назад</a>';
}
else {
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html>
	<head>
		<title>Поиск слов</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<body>
	<form action="words.php" method="POST">
		Слово: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="text" name="searchWord" value="виноград" size="30" maxlength="8">
		<input type="submit" value="Запустить">
		<br>
		Слово должно быть длиной от 4 до 8 символов и содержать только русские буквы.
		<br>
		Работать может достаточно долго (пару минут)
	</form>

	</body>
	</html>
<?php
}
