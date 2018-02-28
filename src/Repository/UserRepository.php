<?php

namespace App\Repository;

use App\Entity\Movie;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param $imdbId
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findMovieWithUser(User $user, $imdbId)
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.movies', 'm')
            ->where('u.id = :id_user')
            ->andWhere('m.imdbID = :id_imdb')
            ->setParameters([
                'id_user' => $user->getId(),
                'id_imdb' => $imdbId
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $user
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserWithMovies(User $user)
    {
        return $this->createQueryBuilder('u')
            ->addSelect('m')
            ->leftJoin('u.movies', 'm')
            ->where('u.id = :id_user')
            ->setParameter('id_user', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Movie $movie
     *
     * @return User[]
     */
    public function getUsersFromMovie(Movie $movie)
    {
        return $this->createQueryBuilder('u')
            ->addSelect('m')
            ->innerJoin('u.movies', 'm')
            ->where('m.id = :id_movie')
            ->setParameter('id_movie', $movie->getId())
            ->getQuery()
            ->getResult();
    }
}
