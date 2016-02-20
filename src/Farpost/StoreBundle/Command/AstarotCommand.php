<?php
namespace Farpost\StoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Farpost\StoreBundle\Daemons\Astarot;

class AstarotCommand extends ContainerAwareCommand
{
    // private $astarot;
    protected function configure()
    {
        $this
            ->setName('astarot')
            ->setDescription('summon astarot');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
                                                    $log = __DIR__ . '/../../../../web/astarot_log.txt';
                                                    Astarot::logInit($log);
        Astarot::memcacheInit();
        if (Astarot::isRunning()) {
            Astarot::restart();
            return;
        }
        $pid = pcntl_fork();
                                                    Astarot::logWrite("forked with pid = $pid");
        if ($pid === -1) {
            throw new \RuntimeException('Could not fork the process');
        } else if ($pid > 0) {
            if (!Astarot::isRunning()) {
                Astarot::writePid($pid);
            }
        } else {
            $this->astarot = new Astarot();
            $this->astarot->init();
            while(!$this->astarot->finish())
            {
                $this->astarot->nextStep();
            }
            Astarot::writePid(-1);
                                                    Astarot::logWrite('Astarot exit');
        }
    }
}