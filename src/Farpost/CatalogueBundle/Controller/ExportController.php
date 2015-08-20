<?php

namespace Farpost\CatalogueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    public function exportAction()
    {
        shell_exec("../app/console catalogue:export > catalogue_export.log 2>catalogue_export_2.log &");
        sleep(1);
        return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    }

    private function performCommand($command)
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => $command
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();
        return new Response($content);
    }

    public function rescueAction()
    {
        return $this->performCommand('catalogue:rescue');
    }

    public function fixBadEdgesAction()
    {
        return $this->performCommand('catalogue:fix');
    }

    public function resetProcessingCatalogueAction()
    {
        return $this->performCommand('catalogue:reset_processing');
    }

    public function warnAction()
    {
        return $this->performCommand('catalogue:warn');
    }
}