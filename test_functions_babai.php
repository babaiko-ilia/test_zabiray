<?php
// Данные для авторизации и получения токена
$array = [
    "username" => "test",
    "password" => "test1234"
];

$ch = curl_init('https://testapi.zabiray.ru/token');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array, '', '&'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);

$auth_data = json_decode($html, true);

// Получаем данные карт клиента
$id = 1; // Здесь можно указать id клиента
$array = array(
    "Authorization: bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJ0ZXN0IiwiZXhwIjoxNjg1MTA1MzMwfQ.3hX4ixyWYKeYOYswekV-FejeJroZ98iBfiFpbj8AwtM"
);

$url = "https://testapi.zabiray.ru/cards";
$data = array('id' => 543);

$options = array(
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($data, '', '&'),
    CURLOPT_HTTPHEADER     => $array
);

$curl = curl_init();
curl_setopt_array($curl, $options);
$cards = curl_exec($curl);
curl_close($curl);

$cards = json_decode($cards, true);;

// Получаем данные последней карты клиента
$last_card = end($cards);
$card_number = $last_card["number"];
$expiry_date = $last_card["expiry_date"];
$expiry_date_timestamp = strtotime($expiry_date);

// Проверяем действительность карты
$today_timestamp = time();

if ($expiry_date_timestamp < $today_timestamp) {
    $message = "Карта $card_number является более не действительной на " . date("d.m.Y", $today_timestamp) . ", так как срок ее действия прошел $expiry_date";
    $button_text = "Продолжить, карта действительная";
} else {
    $message = "Карта $card_number действительна";
    $button_text = "Отправить заявку";
}

// Выводим форму заявки
echo "<form method='post'>";
echo "<h1>Форма заявки</h1>";
echo '<label for="card-number">Номер карты:</label>
  <select id="card-number" name="card-number">
   <option value="1">1111 1111 1111 1111</option>
   <option value="2">2222 2222 2222 2222</option>
   <option value="3">3333 3333 3333 3333</option>
  </select>';
echo "<p>$message</p>";
echo "<button type='submit' name='submit_button'>$button_text</button>";
echo "</form>";

// Обрабатываем отправку заявки
if (isset($_POST["submit_button"]) && $_POST["submit_button"] == $button_text) {
    echo "<p>Заявка успешно отправлена</p>";
} elseif (isset($_POST["submit_button"])) {
    $today_timestamp = time();
    $message = "Продолжайте использовать карту $card_number";
    echo "<p>$message</p>";
}