<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '209003069:AAHvbUfTNpDy-GEsnwp_6MiOu_LaZJBbvuc';
$BOT_NAME = 'minpromtorg_bot';
$hook_url = 'http://195.128.125.243/indexator/hook.php';
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