<?php

namespace Farpost\MapsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GraphImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('graph:import')
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'Graph dump filename'
            )
            ->setDescription('Imports maps graph');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $graphImporter = $this->getContainer()->get('farpost.maps.graph_importer');
        $graphImporter->import($input->getArgument('filename'));
    }
}
