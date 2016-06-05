<?php

require_once __DIR__ . '/vendor' . '/autoload.php';
/**
 * Description of SiteSearch
 *
 * @author Mikhail
 */
use Elasticsearch\ClientBuilder;

class SiteSearch
{

    private $client;
    private $index;
    private $type;
    private $indexSpopFields = [
        "NEWS_ID", "TITLE", "BODY", "URL_ALIAS", "TIMESTAMP"
    ];

    public function __construct($query = null, array $argv = null)
    {
        $this->client = ClientBuilder::create()->build();
        $this->index = "mpt";
        $this->type = "news";


        if (!empty($argv)) {
            $this->argv = array_flip($argv);
            $this->routeCli();
        }

        if (!empty($query)) {
            $this->search($query);
        }
    }

    private function search($query)
    {
        $query = strip_tags($query);
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'match' => [
                        'TITLE' => $query,
                        //"type" => "phrase"
                    ] , 
                    
                ],
                'size' => 100,
                //'type' =>  'phrase'
            ]
        ];



        $response = $this->client->search($params);
		print_r($response);
        //echo "<pre>";
        //print_r($_REQUEST);
        //print_r($params);
        //echo(json_encode(array_merge(["params" => $params] , ["result" => $response['hits']['hits']])));
        //echo "</pre>";
    }

    private function routeCli()
    {
        if (!empty($this->argv['create-index'])) {
            $this->createIndex();
        }
        if (!empty($this->argv['del-index'])) {
            $this->delIndeces();
        }
    }

    /**
     * Залить
     */
    private function createIndex()
    {
		$start = self::microTime();
		//$this->delIndeces();
        $this->log("createIndex");
        //$this->setIndices();
        $this->setIndicesForShop();
        //получить требуемые товары		
		
		require_once __DIR__ . '/indexes.php';
        //обойти товары
        array_map(function($item) {
	        //print_r($item);
            $item['TITLE'] = $this->clearStr($item['TITLE']);
            $item['BODY'] = $this->clearStr($item['BODY']);

            $response = $this->client->index([
                'index' => $this->index,
                'type' => $this->type,
                'id' => $item['NEWS_ID'],
                'body' => $item
            ]);

            //$this->log("add index for " . $item['TITLE']);
        }, $NEWS);
		
		$end = self::microTime();
		
		self::runTime($start , $end , "array : ".count($NEWS));
    }

    /**
     * Создать условия индексации
     */
    private function setIndicesForShop()
    {
         
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'filter' => [
                            'shingle' => [
                                'type' => 'shingle'
                            ],
                        ],
                        'char_filter' => [//"stopwords": "а,без,более,бы,был,была,были,было,быть,в,вам,вас,весь,во,вот,все,всего,всех,вы,где,да,даже,для,до,его,ее,если,есть,еще,же,за,здесь,и,из,или,им,их,к,как,ко,когда,кто,ли,либо,мне,может,мы,на,надо,наш,не,него,нее,нет,ни,них,но,ну,о,об,однако,он,она,они,оно,от,очень,по,под,при,с,со,так,также,такой,там,те,тем,то,того,тоже,той,только,том,ты,у,уже,хотя,чего,чей,чем,что,чтобы,чье,чья,эта,эти,это,я,a,an,and,are,as,at,be,but,by,for,if,in,into,is,it,no,not,of,on,or,such,that,the,their,then,there,these,they,this,to,was,will,with"
                            'pre_negs' => [
                                'type' => 'pattern_replace',
                                'pattern' => '(\\w+)\\s+((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\b',
                                'replacement' => '~$1 $2'
                            ],
                            'post_negs' => [
                                'type' => 'pattern_replace',
                                'pattern' => '\\b((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\s+(\\w+)',
                                'replacement' => '$1 ~$2'
                            ]
                        ],
                        'analyzer' => [
                            'reuters' => [
                                'type' => 'custom',
                                'tokenizer' => 'ngram_tokenizer',
                                'filter' => ['lowercase', 'stop', 'kstem']
                            ],
                            'ngram_analyzer' => [
                                'tokenizer' => 'ngram_tokenizer'
                            ]
                        ],
                        //кастомный tokenizer для ngram
                        "tokenizer" => [
                            "ngram_tokenizer" => [
                                "type" => "nGram",
                                "min_gram" => "2",
                                "max_gram" => "20",
                                "token_chars" => [ "letter", "digit"]
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    '_default_' => [
                        'properties' => [
							'NEWS_ID' => [
                                'type' => 'integer',
                                'index' => 'not_analyzed'
                            ],
                            'TITLE' => [
                                'type' => 'string',
                                'analyzer' => 'reuters',
                                'term_vector' => 'yes',
                                'copy_to' => 'combined'
                            ],
                            'BODY' => [
                                'type' => 'string',
                                'analyzer' => 'reuters',
                                'term_vector' => 'yes',
                                'copy_to' => 'combined'
                            ],
                            //комбинированное поле из основных полей
                            'combined' => [
                                'type' => 'string',
                                'analyzer' => 'reuters',
                                'term_vector' => 'yes'
                            ],
                            'TIMESTAMP' => [
                                'type' => 'integer',
                                'index' => 'not_analyzed'
                            ],
                            'URL_ALIAS' => [
                                'type' => 'string',
                                'index' => 'not_analyzed'
                            ]
                        ]
                    ],

                ]
            ]
        ];
        $this->client->indices()->create($params);
    }
	
	/**
     * тестовые замеры времени работы
     */
    public static function microTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
    
    public static function runTime($start , $end , $name = false){
        print ($name ? : "base").": time:" . round(($end - $start), 4) . "sec \t memory usage:" . self::convert(memory_get_usage(true)) . "\n";
    }
    
    public static function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    private function delIndeces()
    {
        $deleteParams = [
            'index' => $this->index
        ];
        $response = $this->client->indices()->delete($deleteParams);

        print_r($response);
    }

    private function clearStr($string)
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        return $string;
    }

    private function getUrl($id)
    {
        return "product-" . $id;
    }

    private function log($var)
    {
        echo "\n";
        print_r($var);
        echo "\n";
    }

}

if ($_GET['query'] || $argv) {

    $search = new SiteSearch($_GET['query'], $argv);
    exit;
}