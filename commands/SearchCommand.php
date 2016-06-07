<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ForceReply;

/**
 * User "/search" command
 */
class SearchCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'search';
    protected $description = 'search news';
    protected $usage = '/search';
    protected $version = '0.0.1';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();

		$text = isset($argv[2]) ? $argv[2] : 'Для поиска введите /search что ищем';
		
        $data = [];
        $data['chat_id'] = $chat_id;
        $data['text'] = $message->getText(true) ? : 'Для поиска введите /search что ищем';

        return Request::sendMessage($data);
    }
}
