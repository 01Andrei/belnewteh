<?php

header('Access-Control-Allow-Origin: *');


$email = trim($_REQUEST['email'] ?? '');
$name = trim($_REQUEST['name'] ?? '');
$security = trim($_REQUEST['security'] ?? '');

if (empty($email) || empty($name) || empty($security)) {
	echo 'Fail-0';
	die();
}

$data = $_POST;

if (isset($data['security'])) {
	unset($data['security']);
}

@file_put_contents(__DIR__ . '/msg/order-'.date('Y-m-d_H-i').'.txt', print_r($data, 1), FILE_APPEND);

try {
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8\r\n";
	$headers .= "From: <info@belnewteh.by>\r\n";

	$clientName = $name;
	$clientEmail = $email;
	$clientMsg = '';
	$mailMessage = '';

	if (strlen($clientEmail) > 0) {
		$mailMessage = <<<_OUT
                <p>
                    Вы можете также ответить клиенту на имейл: <strong>$clientEmail</strong>.
                    <br/>
                    <a href="mailto:$clientEmail?subject=BelNewTeh.By&body=Здравствуйте!">
                        Ответить клиенту
                    </a>
                </p>
_OUT;
	}

	$mailMessage = <<<_OUT
                <html lang="ru_BY">
                <p>День добрый!</p>
                <p>Посетитель сайта <strong>$clientName</strong> отправил заявку:</p>
                <blockquote>$clientMsg</blockquote>
                $mailMessage
                </html>
_OUT;

	$mailSending = mail(
		'info@belnewteh.by',
		'BelNewTeh.By | Сообщение с сайта от ' . date('Y-m-d H:i:s'),
		$mailMessage,
		$headers
	);

	if ($mailSending) {
		echo 'OK!';
	} else {
		echo 'Cannot send email';
		http_response_code(412);
	}

	die();

} catch (Exception $e) {
	@file_put_contents(__DIR__ . '/logs/log-'.date('Y-m-d_H-i').'.log', print_r($e, 1), FILE_APPEND);

	echo 'Fail-2';
	die();
}

echo 'Fail-3';
die();
