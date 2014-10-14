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
        if (Astarot::isRunning()) {
            Astarot::restart();
            return;
        }
        $pid = pcntl_fork();
        if ($pid === -1) {
            throw new \RuntimeException('Could not fork the process');
        } else if ($pid > 0) {
            if (!Astarot::isRunning()) {
                Astarot::writePid($pid);
            }
        } else {
            error_log("IN ASTAROT ITERATION");
            $this->astarot = new Astarot();            
            $this->astarot->init();
            while(!$this->astarot->finish()) 
            {
                // $output->writeln("ITERATION");/
                $this->astarot->nextStep();
            }
            Astarot::writePid(-1);
        }
    }

    // public function sigHandler($signo)
    // {
        // switch ($signo) 
        // {
            // case SIGTERM:
                // $this->astarot->stop();
                // break;
            // case SIGUSR1:
                // echo json_encode([
                    // 'count'   => $this->astarot->cnt,
                    // 'current' => $this->astarot->cur
                // ]);
                // break;
            // default:
                // Ловим все остальные сигналы
        // }
    // }


}