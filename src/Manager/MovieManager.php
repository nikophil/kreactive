<?php

namespace App\Manager;

use App\Entity\Movie;
use App\Entity\User;
use App\Guzzle\Client;
use Doctrine\ORM\EntityManagerInterface;

class MovieManager
{
    /** @var Client */
    private $client;
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(Client $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $imdbId
     *
     * @return Movie|null
     */
    public function createMovieFromImdbId($imdbId)
    {
        $response = $this->client->request(['i' => $imdbId]);
        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['Error'])) {
            return null;
        }

        $movie = (new Movie())
            ->setImdbID($imdbId)
            ->setTitle($response['Title'])
            ->setPoster($response['Poster']);

        return $movie;
    }

    /**
     * @param User $user
     * @param $imdbId
     *
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function userHasMovie(User $user, $imdbId)
    {
        $userWithMovie =  $this->entityManager
            ->getRepository(User::class)
            ->findMovieWithUser($user, $imdbId);

        return null !== $userWithMovie;
    }

    /**
     * @param $imdbId
     *
     * @param bool $createIfNotExists
     *
     * @return Movie|null
     */
    public function getMovie($imdbId, bool $createIfNotExists = true)
    {
        $movie = $this->entityManager
            ->getRepository(Movie::class)
            ->findOneBy(['imdbID' => $imdbId]);

        if (!$movie && $createIfNotExists) {
            $movie = $this->createMovieFromImdbId($imdbId);
        }

        return $movie;
    }
}
