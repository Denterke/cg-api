<?php
namespace Farpost\StoreBundle\Daemons;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\Container;
// use Farpost\StoreBundle\Utility\
require(__DIR__ . '/../../../../scripts/GetConnect.php');

class Astarot extends ContainerAwareCommand
{
    public $stop;
    public $cnt;
    public $cur;
    private $pdo;
    private $ids;
    private $doStmt;
    private $cntStmt;
    private $recs;
    const REC_PER_ITERATION = 50;
    static public $memcache;
    static public $logName;

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
        $pid = self::get('pid');
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

    static public function getState()
    {
                                                    self::logWrite("getState_1");
        self::memcacheConnect();
                                                    self::logWrite("getState_2");
        if (self::get('cnt') !== false && self::get('cur') !==false) {
                                                    self::logWrite("getState_3");
            $res = [
                'count'   => self::get('cnt'),
                'current' => self::get('cur')
            ];
                                                    self::logWrite("getState_4");
            self::memcacheClose();
                                                    self::logWrite("getState_5_returns " . json_encode($res));
            return $res;
        } else {
                                                    self::logWrite("getState_6");
            self::memcacheClose();
                                                    self::logWrite("getState_7_returns false");
            return false;
        }
    }

    static private function setResetFlag()
    {
                                                    self::logWrite("setResetFlag_1");
        self::memcacheConnect();
                                                    self::logWrite("setResetFlag_2");
        self::set('res', true);
                                                    self::logWrite("setResetFlag_3");
        self::memcacheClose();
                                                    self::logWrite("setResetFlag_4");
    }

    static public function writePid($pid)
    {
                                                    self::logWrite('writePid_1');
        self::memcacheConnect();
                                                    self::logWrite('writePid_2');
        self::set('pid', $pid);
                                                    self::logWrite('writePid_3');
        self::memcacheClose();
                                                    self::logWrite('writePid_4');
    }

    static public function isRunning()
    {
                                                    self::logWrite('isRunning_1');
        self::memcacheConnect();
                                                    self::logWrite('isRunning_2');
        $run = self::get('run');
                                                    self::logWrite('isRunning_3');
        self::memcacheClose();
                                                    self::logWrite('isRunning_4_returns ' . $run);
        return $run;
    }

    static public function restart()
    {
                                                    self::logWrite('restart_1');
        if (self::isRunning()) {
                                                    self::logWrite('restart_2');
            self::setResetFlag();
                                                    self::logWrite('restart_3');
        }
                                                    self::logWrite('restart_4');
    }

    private function checkResetFlag()
    {
                                                    self::logWrite('checkResetFlag_1');
        self::memcacheConnect();
                                                    self::logWrite('checkResetFlag_2');
        if (self::get('res')) {
                                                    self::logWrite('checkResetFlag_3');
            self::set('res', false);
                                                    self::logWrite('checkResetFlag_4');
            self::memcacheClose();
                                                    self::logWrite('checkResetFlag_5');
            pg_close($this->pdo);
                                                    self::logWrite('checkResetFlag_6');
            echo "I'll do it again, mortal!";
                                                    self::logWrite('checkResetFlag_7');
            $this->init();
                                                    self::logWrite('checkResetFlag_8');
            return;
        }
        $this->memcacheClose();
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

    public function updateSchedule()
    {
                                                    self::logWrite('updateSchedule_1');
        $i = 0;
        $insStr = 'INSERT INTO schedule_rendered (exec_date, schedule_id)';
        $valStr = '';
        $ids = '';
        while ($i < self::REC_PER_ITERATION && $i < count($this->recs) && $this->cur < count($this->recs)) {
            $schedule = $this->recs[$this->cur];
            $i++;
            $this->cur++;
            $current_time = \DateTime::createFromFormat('Y-m-d', $schedule['st']);
            // $current_time = new \DateTime();
            $dow = $schedule['day'];
            $current_dow = date("N", $current_time->getTimestamp());
            $period = $schedule['period'];
            $end_time = \DateTime::createFromFormat('Y-m-d', $schedule['et']);
            if ($dow < $current_dow) {
               $dow += $period;
               $current_time = $current_time->add(new \DateInterval('P' . $period . 'D'));
            }
            while ($dow != $current_dow) {
               $current_dow++;
               $current_time = $current_time->add(new \DateInterval('P' . 1 . 'D'));
            }
            while ($current_time <= $end_time) {
                if ($valStr !== '') {
                    $valStr .= ', ';
                }
                $valStr .= "('" . $current_time->format('Y-m-d') . "', $schedule[id])";
                $current_time = $current_time->add(new \DateInterval('P' . $period . 'D'));
            }
            if ($ids !== '') {
                $ids .= ', ';
            }
            $ids .= $schedule['id'];
            // array_shift($this->recs);
        }
        if ($valStr === '') {
            return;
        }
        $result = pg_query(
            $this->pdo,
            "$insStr VALUES $valStr"
        );
        // echo "ids: $ids\n";
        $result = pg_query(
            $this->pdo,
            "UPDATE schedule SET status = 1 WHERE id IN ($ids)"
        );
                                                    self::logWrite('updateSchedule_2');
    }



    public function __construct()
    {
                                                    self::logWrite('__construct_1');
        self::memcacheConnect();
                                                    self::logWrite('__construct_2');
        $run = self::get('run');
                                                    self::logWrite('__construct_3');
        if ($run) {
                                                    self::logWrite('__construct_4');
            $this->memcacheClose();
                                                    self::logWrite('__construct_5');
            die("You have already summon me, mortal!\n");
        }
                                                    self::logWrite('__construct_6');
        self::set('run', true);
                                                    self::logWrite('__construct_7');
        self::memcacheClose();
                                                    self::logWrite('__construct_8');
    }

    private function updCnt()
    {
                                                    self::logWrite('updCnt_1');
        if ($this->pdo) {
            $result = pg_execute($this->pdo, 'select_all', []);
            $this->recs = pg_fetch_all($result);
            if ($this->recs === false) {
                $this->cnt = 0;
            } else {
                $this->cnt = count($this->recs);
            }
        } else {
            $this->cnt = -1;
        }
                                                    self::logWrite("updCnt_2_cnt = {$this->cnt}");
    }

    public function init()
    {
                                                    self::logWrite("init_1");
        $this->stop = false;
                                                    self::logWrite("init_2");
        $this->cur = 0;
                                                    self::logWrite("init_3");
        $res = getConnection();
                                                    self::logWrite("init_4");
        $this->pdo = $res[0];
                                                    self::logWrite("init_4_con = " . json_encode($res[1]));
                                                    self::logWrite("init_5");
        if (pg_ErrorMessage($this->pdo)) {
                                                    self::logWrite("init_6");
            echo "error with db";
                                                    self::logWrite("init_7");
        }
                                                    self::logWrite("init_8");
        $this->cntStmt = pg_prepare(
            $this->pdo,
            'select_all',
            "SELECT 
                s.id as id, sm.time_start as st, sm.time_end as et, s.period as period, s.day as day
            FROM
                semesters sm INNER JOIN schedule s ON sm.id = s.semester_id
            WHERE
                s.status = 0;"
        );
                                                    self::logWrite("init_9");
        $this->updCnt();
                                                    self::logWrite("init_10");
    }

    private function writeToMem()
    {
                                                    self::logWrite("writeToMem_1");
        self::memcacheConnect();
                                                    self::logWrite("writeToMem_2");
        self::set('cur', $this->cur);
                                                    self::logWrite("writeToMem_3");
        self::set('cnt', $this->cnt);
                                                    self::logWrite("writeToMem_4");
        self::memcacheClose();
                                                    self::logWrite("writeToMem_5");
    }

    public function nextStep()
    {
                                                    self::logWrite("nextStep_1");
        $this->updateSchedule();
                                                    self::logWrite("nextStep_2");
        $this->checkResetFlag();
                                                    self::logWrite("nextStep_3");
        $this->writeToMem();
                                                    self::logWrite("nextStep_4");
    }

    public function finish()
    {
                                                    self::logWrite("finish_1");
        if ($this->stop || $this->cur >= $this->cnt) {
                                                    self::logWrite("finish_2");
            pg_close($this->pdo);
                                                    self::logWrite("finish_3");
            if (!$this->stop) {
                                                    self::logWrite("finish_4");
                $this->stop();
                                                    self::logWrite("finish_5");
            }
                                                    self::logWrite("finish_6_returns true");
            return true;
        }
                                                    self::logWrite("finish_7_returns false");
        return false;
    }

    public function stop()
    {
                                                    self::logWrite("stop_1");
        $this->stop = true;
                                                    self::logWrite("stop_2");
        self::memcacheConnect();
                                                    self::logWrite("stop_3");
        if (self::get('run')) {
                                                    self::logWrite("stop_4");
            self::set('run', false);
                                                    self::logWrite("stop_5");
        }
                                                    self::logWrite("stop_6");
        self::memcacheClose();
                                                    self::logWrite("stop_7");
        $this->cur = 0;
        $this->cnt = 0;
                                                    self::logWrite("stop_8");
        $this->writeToMem();
                                                    self::logWrite("stop_9");
        echo "I'll wait you in da Hell, mortal!\n";
                                                    self::logWrite("stop_10");
    }
}