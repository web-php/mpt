<?php
//README
//This configuration file is intented to run the bot with the webhook method
//Uncommented parameters must be filled

#bash script
#while true; do ./getUpdatesCLI.php; done

// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '208257219:AAHaylq9HW7Xo2EhbB4fKHU0PqBJwWD1SvI';
$BOT_NAME = 'minpromtorg_bot';
$commands_path = "commands/";


try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Enable MySQL
    // $telegram->enableMySQL($mysql_credentials);

    //// Enable MySQL with table prefix
    //$telegram->enableMySQL($mysql_credentials, $BOT_NAME . '_');

    //// Add an additional commands path
    $telegram->addCommandsPath($commands_path);

    //// Here you can enable admin interface for the channel you want to manage
    //$telegram->enableAdmins(['212034823']);
    //$telegram->setCommandConfig('sendtochannel', ['your_channel' => '@type_here_your_channel']);

    //// Here you can set some command specific parameters,
    //// for example, google geocode/timezone api key for date command:
    //$telegram->setCommandConfig('date', ['google_api_key' => 'your_google_api_key_here']);

    //// Logging
    $telegram->setLogRequests(true);
    $telegram->setLogPath($BOT_NAME . '.log');
    $telegram->setLogVerbosity(3);

    //// Set custom Upload and Download path
    //$telegram->setDownloadPath('../Download');
    //$telegram->setUploadPath('../Upload');

    // Handle telegram getUpdate request
    $ServerResponse = $telegram->handleGetUpdates();

    if ($ServerResponse->isOk()) {
        $n_update = count($ServerResponse->getResult());
        print(date('Y-m-d H:i:s', time()) . ' - Processed ' . $n_update . ' updates' . "\n");
    } else {
        print(date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . "\n");
        echo $ServerResponse->printError() . "\n";
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    echo $e;
}
