<?php
/**
 * Created by IntelliJ IDEA.
 * User: kalita
 * Date: 29/07/15
 * Time: 10:54
 */

namespace Farpost\NewsBundle\Entity;


use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
    /**
     * @param \DateTime $baseDatetime
     * @param $count
     *
     * @return mixed
     */
    public function getList(\DateTime $baseDatetime = null, $count)
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.published = true')
            ->setMaxResults(abs($count))
        ;

        if ($baseDatetime != null) {
            $qb->andWhere('a.dt < :baseDatetime')
                ->setParameter('baseDatetime', $baseDatetime);
        }
        $qb->orderBy('a.dt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}