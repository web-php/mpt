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
use Elasticsearch\ClientBuilder;

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
		$data['parse_mode'] = 'HTML';
        $data['text'] = $message->getText(true) ? $this->executeSearch($message->getText(true)) : 'Для поиска введите /search что ищем';

        return Request::sendMessage($data);
    }
	
	private function executeSearch($query)
	{
		$hosts = [
			'195.128.125.243:9200',         // IP + Port
		];
		$this->index = "mpt";
        $this->type = "news";
		$this->client = ClientBuilder::create()
		->setHosts($hosts)
		->build();
		
		
		$query = strip_tags($query);
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'match' => [
                        'TITLE' => trim($query),
                        //"type" => "phrase"
                    ] , 
                    
                ],
                'size' => 10,
                //'type' =>  'phrase'
            ]
        ];
		
		$response = $this->client->search($params);
		$out = "";
		foreach($response['hits']['hits'] as $news){
			$out .= '
			<a href="http://minpromtorg.gov.ru/press-centre/news/#!'.$news['_source']['NEWS_ID'].'">'.trim($news['_source']['TITLE']).'</a>
			';
		}
		return $out; 
		//"<pre>".print_r($response,true)."</pre>";
	}
}
