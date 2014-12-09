<?php
namespace Farpost\StoreBundle\Daemons;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\Container;
require(__DIR__ . '/../../../../scripts/GetConnect.php');

class Paimon extends ContainerAwareCommand
{
    static public $memcache;
    static public $logName;
    const FEFU_URL = "https://api.vk.com/method/wall.get?owner_id=-28511920&count=50";

    static public function logInit($filename)
    {
        self::$logName = $filename;
        self::logWrite('logInit');
    }

    static public function logWrite($str)
    {
        $dt = new \DateTime();
        file_put_contents(self::$logName, $dt->format('d-m-Y, G:i:s') . ">> $str\n", FILE_APPEND);
    }

    static public function getFTok()
    {
        return ftok(__FILE__, 'A');
    }

    static public function getPid()
    {
                                                    self::logWrite("getPid_1");
        self::memcacheConnect();
                                                    self::logWrite("getPid_2");
        $pid = self::get('paimon_pid');
                                                    self::logWrite("getPid_3");
        self::memcacheClose();
                                                    self::logWrite("getPid_4");
        return $pid;
    }

    static public function memcacheInit()
    {
                                                    self::logWrite('memcacheInit_1');
        if (!self::$memcache) {
                                                    self::logWrite('memcacheInit_2');
            self::$memcache = new \Memcache;
                                                    self::logWrite('memcacheInit_3');
        }
                                                    self::logWrite('memcacheInit_4');
    }

    static public function writePid($pid)
    {
                                                    self::logWrite('writePid_1');
        self::memcacheConnect();
                                                    self::logWrite('writePid_2');
        self::set('paimon_pid', $pid);
                                                    self::logWrite('writePid_3');
        self::memcacheClose();
                                                    self::logWrite('writePid_4');
    }

    static public function isRunning()
    {
                                                    self::logWrite('isRunning_1');
        self::memcacheConnect();
                                                    self::logWrite('isRunning_2');
        $run = self::get('paimon_run');
                                                    self::logWrite('isRunning_3');
        self::memcacheClose();
                                                    self::logWrite('isRunning_4_returns ' . $run);
        return $run;
    }

    static public function isStopped()
    {
        self::memcacheConnect();
        $stop = self::get('paimon_stop');
        self::memcacheClose();
        return $stop;
    }

    static public function memcacheConnect()
    {
                                                    self::logWrite('memcacheConnect_1');
        self::$memcache->connect('localhost') or
            die('Can not connect memcache server');
                                                    self::logWrite('memcacheConnect_2');
    }

    static public function memcacheClose()
    {
                                                    self::logWrite('memcacheClose_1');
        self::$memcache->close() or
            die('Can not close memcache connection');
                                                    self::logWrite('memcacheClose_2');
    }

    static public function get($key)
    {
                                                    self::logWrite('get_1');
        $res = self::$memcache->get($key);
                                                    self::logWrite("get_2_returns $res");
        return $res;
    }

    static public function set($key, $val)
    {
                                                    self::logWrite('set_1');
        self::$memcache->set($key, $val);
                                                    self::logWrite('set_2');
    }

    public function parse()
    {
        //get ids
        $res = getConnection();
        $pdo = $res[0];
        $result = pg_query($pdo, "SELECT vk_id FROM news;");
        $tmp = pg_fetch_all($result);
        $ids = [];
        if ($tmp !== false) {
            foreach($tmp as $row) {
                $ids[] = $row['vk_id'];
            }
        }
        $news = json_decode(file_get_contents(self::FEFU_URL), true)['response'];
        array_shift($news); //first element - count of all records
        pg_prepare(
            $pdo,
            'news_insert',
            "INSERT INTO news (vk_id, dt, title, body, active, main_img) 
             VALUES ($1, $2, $3, $4, $5, $6)
             RETURNING id"
        );
        pg_prepare(
            $pdo,
            'imgs_insert',
            "INSERT INTO images (src, src_big, src_small, width, height, news_id) 
             VALUES ($1, $2, $3, $4, $5, $6)"
        );
        pg_prepare(
            $pdo,
            'links_insert',
            "INSERT INTO links (url, title, news_id)
             VALUES ($1, $2, $3)"
        );
        foreach ($news as $new_item) {
            if (is_array($new_item)) {
                if ($new_item['post_type'] !== 'post') {
                    continue;
                }
                if (in_array($new_item['id'], $ids)) {
                    continue;
                }
                if (
                    array_key_exists('attachment', $new_item) &&
                    array_key_exists('photo', $new_item['attachment']) &&
                    array_key_exists('src_big', $new_item['attachment']['photo'])
                ) {
                    $img = self::saveImg($new_item['attachment']['photo']['src_big']);
                } else {
                    $img = null;
                }
                $resource = pg_execute(
                    $pdo,
                    'news_insert',
                    [
                        $new_item['id'],
                        $new_item['date'],
                        '',
                        preg_replace(
                            '/\<.*?\>/',
                            '',
                            preg_replace(
                                '/\<br\>/',
                                "\n",
                                $new_item['text']
                            )
                        ),
                        true,
                        $img
                    ]
                );
                list($newsId) = pg_fetch_row($resource);
            }
            if (!array_key_exists('attachments', $new_item)) {
                continue;
            }
            foreach($new_item['attachments'] as $attach) {
                switch ($attach['type']) {
                    case 'photo':
                        $src = self::saveImg($attach['photo'], 'src');
                        $srcBig = self::saveImg($attach['photo'], 'src_big');
                        $srcSmall = self::saveImg($attach['photo'], 'src_small');
                        pg_execute(
                            $pdo,
                            'imgs_insert',
                            [
                                "$src",
                                "$srcBig",
                                "$srcSmall",
                                $attach['photo']['width'],
                                $attach['photo']['height'],
                                $newsId
                            ]
                        );
                        break;
                    case 'link':
                        pg_execute(
                            $pdo,
                            'links_insert',
                            [
                                $attach['link']['url'],
                                trim($attach['link']['title']) ? $attach['link']['title'] : '',
                                $newsId
                            ]
                        );
                        break;
                }
            }
        }  
        pg_close($pdo);
    }

    function saveImg($url, $key = null)
    {
        if ($key !== null) {
            if (array_key_exists($key, $url)) {
                $url = $url[$key];
            } else {
                self::logWrite('such key doesnt exist');
                return '';
            }
        }
        $imgFolder = __DIR__ . '/../../../../web/static/newsImgs/';
        $filename = basename($url);
        if (!is_dir($imgFolder)) {
            mkdir($imgFolder);
        }
        if (!empty($filename)) {
            if (!file_exists("$imgFolder/$filename")) {
                file_put_contents("$imgFolder/$filename", file_get_contents($url));
            }
        }
        return $filename;
    }

    public function __construct()
    {
                                                    self::logWrite('__construct_1');
        self::memcacheConnect();
                                                    self::logWrite('__construct_2');
        $run = self::get('paimon_run');
                                                    self::logWrite('__construct_3');
        if ($run) {
                                                    self::logWrite('__construct_4');
            $this->memcacheClose();
                                                    self::logWrite('__construct_5');
            die("You have already summon me, mortal!\n");
        }
                                                    self::logWrite('__construct_6');
        self::set('paimon_run', true);
                                                    self::logWrite('__construct_7');
        self::memcacheClose();
                                                    self::logWrite('__construct_8');
    }

    static public function stop()
    {
        self::memcacheConnect();
        self::set('paimon_run', false);
        self::set('paimon_stop', false);
        self::writePid(-1);
        self::memcacheClose();
    }
}