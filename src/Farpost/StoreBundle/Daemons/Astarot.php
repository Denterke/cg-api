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

    static public function getFTok()
    {
        return ftok(__FILE__, 'A');
    }

    static public function getPid()
    {
        self::memcacheConnect();
        $pid = self::get('pid');
        self::memcacheClose();
        return $pid;
    }

    static public function memcacheInit()
    {
        if (!self::$memcache) {
            self::$memcache = new \Memcache; 
        }
    }

    static public function getState()
    {
        self::memcacheConnect();
        if (self::get('cnt') !== false && self::get('cur') !==false) {
            $res = [
                'count'   => self::get('cnt'),
                'current' => self::get('cur')
            ];
            self::memcacheClose();
            return $res;
        } else {
            self::memcacheClose();
            return false;
        }
    }

    static private function setResetFlag()
    {
        self::memcacheConnect();
        self::set('res', true);
        self::memcacheClose();
    }

    static public function writePid($pid)
    {
        self::memcacheConnect();
        self::set('pid', $pid);
        self::memcacheClose();
    }

    static public function isRunning()
    {
        self::memcacheConnect();
        $run = self::get('run');
        self::memcacheClose();
        return $run;
    }

    static public function restart()
    {
        if (self::isRunning()) {
            self::setResetFlag();
        }
    }

    private function checkResetFlag()
    {
        self::memcacheConnect();
        if (self::get('res')) {
            self::set('res', false);
            self::memcacheClose();
            pg_close($this->pdo);
            echo "I'll do it again, mortal!";
            $this->init();
            return;
        }
        $this->memcacheClose();
    }

    static public function memcacheConnect()
    {
        self::$memcache->connect('localhost') or die('Can not connect memcache server');
    }

    static public function memcacheClose()
    {
        self::$memcache->close() or die ('Can not close memcache connection');
    }

    static public function get($key)
    {
        return self::$memcache->get($key);
    }

    static public function set($key, $val)
    {
        self::$memcache->set($key, $val);
    }

    public function updateSchedule()
    {
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
        echo "ids: $ids\n";
        $result = pg_query(
            $this->pdo,
            "UPDATE schedule SET status = 1 WHERE id IN ($ids)"
        );
    }



    public function __construct()
    {
        self::memcacheConnect();
        $run = self::get('run');
        if ($run) {
            $this->memcacheClose();
            die("You have already summon me, mortal!\n");
        }
        self::set('run', true);
        self::memcacheClose();
    }

    private function updCnt()
    {
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
    }

    public function init()
    {
        $this->stop = false;
        $this->cur = 0;
        $res = getConnection();
        $this->pdo = $res[0];
        if (pg_ErrorMessage($this->pdo)) {
            echo "error with db";
        }
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
        $this->updCnt();
    }

    private function writeToMem()
    {
        self::memcacheConnect();
        self::set('cur', $this->cur);
        self::set('cnt', $this->cnt);
        self::memcacheClose();
    }

    public function nextStep()
    {
        $this->updateSchedule();
        $this->checkResetFlag();
        $this->writeToMem();
    }

    public function finish()
    {
        if ($this->stop || $this->cur >= $this->cnt) {
            pg_close($this->pdo);
            if (!$this->stop) {
                $this->stop();
            }
            return true;
        }
        return false;
    }

    public function stop()
    {
        $this->stop = true;
        self::memcacheConnect();
        if (self::get('run')) {
            self::set('run', false);
        }
        self::memcacheClose();
        $this->cur = 0;
        $this->cnt = 0;
        $this->writeToMem();
        echo "I'll wait you in da Hell, mortal!\n";
    }
}        