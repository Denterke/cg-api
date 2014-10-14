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
    private $stmt;
    const REC_PER_ITERATION = 10;
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
        $result = pg_execute($this->pdo, 'renderFoo', []);
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
            $result = pg_execute($this->pdo, 'select_cnt', []);
            $this->cnt = pg_fetch_all($result)[0]['count'];
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
            'select_cnt',
            'SELECT count(*) FROM schedule WHERE status = 0;'
        );
        $this->doStmt = pg_prepare(
            $this->pdo,
            'renderFoo',
            "SELECT * FROM renderAllSchedule(" . self::REC_PER_ITERATION . ");"
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
        $this->updateSchedule();
        $this->cur += self::REC_PER_ITERATION;
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
        echo "I'll wait you in da Hell, mortal!\n";
    }
}        