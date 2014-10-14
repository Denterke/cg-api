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
            'run' => [1, 0],
            'cur' => [2, 0],
            'cnt' => [3, 0],
            'pid' => [4, -1],
            'res' => [5, 0]
        ];
        $astarotMemId = shm_attach(Astarot::GetFTok());
        foreach($var as $key => $val) {
            if (shm_has_var($astarotMemId, $val[0])) {
                echo "Variable $key = " . shm_get_var($astarotMemId, $val[0]) . "\n";
            } else {
                echo "Variable $key does not exist\n";
            }
            if ($input->getOption('clear')) {
                shm_put_var($astarotMemId, $val[0], $val[1]);
                echo "cleared with value: $val[1]\n";
            }
        }
    }
}