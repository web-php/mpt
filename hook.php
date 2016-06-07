<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '208257219:AAHaylq9HW7Xo2EhbB4fKHU0PqBJwWD1SvI';
$BOT_NAME = 'minpromtorg_bot';
$commands_path = "commands/";
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);
	$telegram->addCommandsPath($commands_path);
    // Handle telegram webhook request
    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    echo $e;
}