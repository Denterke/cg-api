<?php

namespace Farpost\CatalogueBundle\Command;

use Farpost\StoreBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetProcessingCatalogueCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('catalogue:reset_processing')
            ->setDescription('Reset catalogue processing status');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $processing = $em->getRepository('FarpostStoreBundle:Version')
            ->findBy(['isProcessing' => true, 'type' => Version::CATALOG_V2]);

        foreach($processing as $object) {
            $object->setIsProcessing(false);
        }

        $em->flush();
    }
}
