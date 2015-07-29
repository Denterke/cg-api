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
        shell_exec("../app/console catalogue:export > /dev/null 2>/dev/null &");
        sleep(1);
        return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    }

    public function rescueAction()
    {
        $kernel = $this->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'catalogue:rescue'
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();
        return new Response($content);
    }
}