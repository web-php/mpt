<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '208257219:AAHaylq9HW7Xo2EhbB4fKHU0PqBJwWD1SvI';
$BOT_NAME = 'minpromtorg_bot';
$hook_url = 'https://mpt-telegram-bot.herokuapp.com/hook.php';
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Set webhook
    $result = $telegram->setWebHook($hook_url);
    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}