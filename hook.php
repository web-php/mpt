<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '209003069:AAHvbUfTNpDy-GEsnwp_6MiOu_LaZJBbvuc';
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