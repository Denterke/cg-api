<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 03/08/15
 * Time: 11:56
 */

namespace Farpost\CatalogueBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CatalogueImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('catalogue:import')
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'Graph dump filename'
            )
            ->setDescription('Drops existent database and restores new one')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('farpost_catalogue.catalogue_importer')->import($input->getArgument('filename'));
    }
}