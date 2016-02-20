<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 16/07/15
 * Time: 10:40
 */

namespace Farpost\MapsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LevelRepository extends EntityRepository
{
    public function generate()
    {
        $batchSize= 20;
        for ($i = 0; $i <= Level::MAX_LEVEL_NUMBER; $i++) {
            $level = new Level();
            $level->setId($i)
                ->setLevel($i)
                ->setAlias("$i уровень")
            ;
            $this->_em->merge($level);
            if ($i % $batchSize === 0) {
                $this->_em->flush();
                $this->_em->clear();
            }
        }
        $this->_em->flush();
    }
}