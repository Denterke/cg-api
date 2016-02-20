<?php
namespace Farpost\StoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Farpost\StoreBundle\Entity\Role;

class LoadRoleData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $roles = ['ADMIN', 'USER'];
        foreach($roles as $roleAlias) {
            $role = $manager->getRepository('FarpostStoreBundle:Role')
                ->findOneBy(['alias' => $roleAlias]);
            if (is_null($role)) {
                $role = new Role();
                $role->setAlias($roleAlias);
                $manager->persist($role);
                $manager->flush();
            }
        }
    }
}