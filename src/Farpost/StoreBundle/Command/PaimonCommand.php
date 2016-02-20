<?php
namespace Farpost\StoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Farpost\StoreBundle\Daemons\Paimon;

class PaimonCommand extends ContainerAwareCommand
{
    private $paimon;
    protected function configure()
    {
        $this
            ->setName('paimon')
            ->setDescription('summon paimon');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
                                                    $log = __DIR__ . '/../../../../web/paimon_log.txt';
                                                    Paimon::logInit($log);
        Paimon::memcacheInit();
        if (Paimon::isRunning()) {
            return;
        }
        $pid = pcntl_fork();
                                                    Paimon::logWrite("forked with pid = $pid");
        if ($pid === -1) {
            throw new \RuntimeException('Could not fork the process');
        } else if ($pid > 0) {
            if (!Paimon::isRunning()) {
                Paimon::writePid($pid);
            }
        } else {
            $this->paimon = new Paimon();
            while(!Paimon::isStopped())
            {
                $this->paimon->parse();
                sleep(60 * 15);
            }
            Paimon::stop();
                                                    Paimon::logWrite('Paimon exit');
        }
    }
}