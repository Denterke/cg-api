<?php
namespace Farpost\StoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Farpost\StoreBundle\Daemons\Astarot;

class ClearShMemCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('csmc')
            ->setDescription('clear shared memory cache')
            ->addOption('clear', null, InputOption::VALUE_NONE, 'If set, shared memory will be clear');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $var = [
            'run' => 0,
            'cur' => 0,
            'cnt' => 0,
            'pid' => -1,
            'res' => 0,
            'paimon_run' => 0,
            'paimon_pid' => -1,
            'paimon_stop' => 0
        ];
        $memcache = new \Memcache;
        $memcache->connect('localhost') or die('Can not connect memcache server');
        foreach($var as $key => $val) {
            $memVal = $memcache->get($key);
            echo "Variable $key = $memVal";
            if ($input->getOption('clear')) {
                $memcache->set($key, $val);
                echo " => $val";
            }
            echo "\n";
        }
        $memcache->close() or die('Can not close connection');
    }
}