<?php

namespace Farpost\MapsBundle\Command;

use Farpost\StoreBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetProcessingGraphCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('graph:reset_processing')
            ->setDescription('Resets graph processing status');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $processing = $em->getRepository('FarpostStoreBundle:Version')
            ->findBy(['isProcessing' => true, 'type' => Version::GRAPH_DUMP]);

        foreach($processing as $object) {
            $object->setIsProcessing(false);
        }

        $em->flush();
    }
}
