<?php

namespace Cac\BarBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PromoOffertesRepository
 *
 */
class PromoOffertesRepository extends EntityRepository
{
    public function getDayPromo($barId, $userId, $date) {
        $query = $this->createQueryBuilder('p')
            ->select('p.id')
            ->where('p.bar = :barId')
            ->andWhere('p.user = :userId')
            ->andWhere('SUBSTRING(p.createdAt, 1, 10) = :tDate')
            ->setParameters(array('barId' => $barId, 'userId' => $userId, 'tDate' => $date));
        
        $result = $query->getQuery()
                        ->getArrayResult();
        return $result;
    }
}
