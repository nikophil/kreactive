<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class MovieRepository extends EntityRepository
{
    public function findBestMovie()
    {
        return $this->createQueryBuilder('m')
            ->select('count(u.id) as nbUsers, m.imdbID, m.title')
            ->innerJoin('m.users', 'u')
            ->groupBy('m.id')
            ->orderBy('nbUsers', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleResult();
    }
}
