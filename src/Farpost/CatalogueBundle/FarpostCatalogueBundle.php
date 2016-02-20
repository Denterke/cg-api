<?php

namespace Farpost\CatalogueBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FarpostCatalogueBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataAdminBundle';
    }
}
