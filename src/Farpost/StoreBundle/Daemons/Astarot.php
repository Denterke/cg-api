<?php
namespace Farpost\StoreBundle\Daemons;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\DependencyInjection\Container;
// use Farpost\StoreBundle\Utility\
require(__DIR__ . '/../../../../scripts/GetConnect.php');

class Astarot extends ContainerAwareCommand
{
    public $stop;
    public $id;
    public $shmId;
    public $cnt;
    public $cur;
    private $pdo;
    private $ids;
    private $doStmt;
    private $cntStmt;
    private $recs;
    const REC_PER_ITERATION = 50;
    static private $v = [
        'run' => 1,
        'cur' => 2,
        'cnt' => 3,
        'pid' => 4,
        'res' => 5
    ];

    static public function getFTok()
    {
        return ftok(__FILE__, 'A');
    }

    static public function getPid()
    {
        $shmId = shm_attach(self::getFTok());
        if (shm_has_var($shmId, self::$v['pid'])) {
            $pid = shm_get_var($shmId, self::$v['pid']);
            return $pid > 0 ? $pid : false;
        }
        return false;
    }

    static public function getState()
    {
        $shmId = shm_attach(self::getFTok());
        if (shm_has_var($shmId, self::$v['cnt']) && shm_has_var($shmId, self::$v['cur'])) {
            return [
                'count' => shm_get_var($shmId, self::$v['cnt']),
                'current' => shm_get_var($shmId, self::$v['cur'])
            ];
        } else {
            return false;
        }
    }

    static private function setResetFlag()
    {
        $shmId = shm_attach(self::getFTok());
        shm_put_var($shmId, self::$v['res'], 1);

    }

    static public function writePid($pid)
    {
        $shmId = shm_attach(self::getFTok());
        shm_put_var($shmId, self::$v['pid'], $pid);
    }

    static public function isRunning()
    {
        $shmId = shm_attach(self::getFTok());
        if (shm_has_var($shmId, self::$v['run'])) {
            return shm_get_var($shmId, self::$v['run']);
        }
        return false;
    }

    static public function restart()
    {
        $shmId = shm_attach(self::getFTok());
        if (self::isRunning()) {
            self::setResetFlag();
        }
    }

    private function checkResetFlag()
    {
        if (shm_has_var($this->shmId, self::$v['res'])) {
            if (shm_get_var($this->shmId, self::$v['res'])) {
                shm_put_var($this->shmId, self::$v['res'], 0);
                pg_close($this->pdo);
                echo "I'll do it again, mortal!";
                $this->init();
            }
        }
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
        $this->id = ftok(__FILE__, 'A');
        $this->shmId = shm_attach($this->id);
        if (shm_has_var($this->shmId, self::$v['run'])) {
            if (shm_get_var($this->shmId, self::$v['run'])) {
                die("You have already summon me, mortal!\n");
            }
        }
        shm_put_var($this->shmId, self::$v['run'], 1);            
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
        shm_put_var($this->shmId, self::$v['cur'], $this->cur);
        shm_put_var($this->shmId, self::$v['cnt'], $this->cnt);
    }

    public function nextStep()
    {
        // echo "before {$this->cur} : {$this->cnt} : " . count($this->recs) . "\n";
        $this->updateSchedule();
        // echo "after {$this->cur} : {$this->cnt} : " . count($this->recs) . "\n";
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
        if (shm_has_var($this->shmId, self::$v['run'])) {
            shm_put_var($this->shmId, self::$v['run'], 0);
        }
        $this->cur = 0;
        $this->cnt = 0;
        $this->writeToMem();
        echo "I'll wait you in da Hell, mortal!\n";
    }
}        