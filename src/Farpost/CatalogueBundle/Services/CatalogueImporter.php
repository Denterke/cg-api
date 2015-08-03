<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 03/08/15
 * Time: 11:58
 */

namespace Farpost\CatalogueBundle\Services;


class CatalogueImporter
{
    private $owner;
    private $databaseName;

    public function __construct($databaseName, $owner)
    {
        $this->owner = $owner;
        $this->databaseName = $databaseName;
    }

    public function import($filename)
    {
        //you should run:
        // app/console doctrine:database:drop --force
        // app/console doctrine:database:create
        system("/usr/bin/psql --host=localhost -U {$this->owner} {$this->databaseName} < $filename");
        // app/console doctrine:schema:update --force
    }

}