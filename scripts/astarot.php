<?php
    /**
     * @link 
     */
    require_once("GetConnect.php");
    declare(ticks = 1);
    
    class Astarot {
        public $stop;
        public $id;
        public $shmId;
        public $cnt;
        public $cur;
        private $pdo;
        private $ids;
        private $stmt;
        static private $v = [
            'run' => 1,
            'cur' => 2,
            'cnt' => 3
        ];

        public function updateSchedule()
        {
            if (empty($this->ids)) {
                exit;
            }
            $result = pg_execute($this->pdo, 'renderFoo', [$this->ids[0]['id']]);
            array_shift($this->ids);
        }

        public function __construct()
        {
            $this->id = ftok(__FILE__, 'A');
            // echo __DIR__;
            $this->shmId = shm_attach($this->id);
            if (shm_has_var($this->shmId, self::$v['run'])) {
                if (shm_get_var($this->shmId, self::$v['run'])) {
                    die("You have already summon me, mortal!\n");
                }
            }
            shm_put_var($this->shmId, self::$v['run'], true);            
        }

        public function init()
        {
            $this->stop = false;
            $this->cur = 0;
            $this->cnt = 1000;
            // $res = getConnection();
            // $this->pdo = $res[0];
            // $result = pg_query($this->pdo, 'SELECT id FROM SCHEDULE WHERE status = 0;');
            // $this->ids = pg_fetch_all($result);
            // if ($this->ids === false) {
                // $this->stop();
                // exit;
            // }
            // $this->cnt = count($this->ids);
            // $this->stmt = pg_prepare(
                // $this->pdo,
                // 'renderFoo',
                // -- 'SELECT * FROM renderSchedule($1)'
            // );
        }

        private function writeToMem()
        {
            shm_put_var($this->shmId, self::$v['cur'], $this->cur);
            shm_put_var($this->shmId, self::$v['cnt'], $this->cnt);
        }

        public function nextStep()
        {
            // $this->updateSchedule();
            $this->writeToMem();
            $this->cnt++;
        }

        public function finish()
        {
            return $this->stop || $this->cur == $this->cnt;
        }

        public function stop()
        {
            // pg_close($this->pdo);
            $this->stop = true;
            if (shm_has_var($this->shmId, self::$v['run'])) {
                shm_put_var($this->shmId, self::$v['run'], false);
            }
            echo "I'll wait you in da Hell, mortal!\n";
        }
    }


    function sigHandler($signo)
    {
        global $astarot;
        switch ($signo) 
        {
            case SIGTERM:
                shm_remove($astarot->shmId);
                $astarot->stop();
                break;
            case SIGUSR1:
                echo json_encode([
                    'count'   => $astarot->cnt,
                    'current' => $astarot->cur
                ]);
                break;
            default:
                // Ловим все остальные сигналы
        }
    }

    // Регистрируем сигналы
    pcntl_signal(SIGTERM, "sigHandler");
    pcntl_signal(SIGUSR1, "sigHandler");


    $astarot = null;

    $pid = pcntl_fork();
    if ($pid == -1) 
    {
        die('could not fork'.PHP_EOL);
    } 
    else if ($pid) 
    {
        die('die parent process'.PHP_EOL);
    } 
    else 
    {
        $astarot = new Astarot();
        $astarot->init();
        while(!$astarot->finish()) 
        {
            sleep(1);
            // error_log("astarot here")
            $astarot->nextStep();
        }
    }
    posix_setsid();