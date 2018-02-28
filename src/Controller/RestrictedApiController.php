<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Manager\MovieManager;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RestrictedApiController
 * @Route("/api")
 */
class RestrictedApiController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/test", name="test-restricted-api", methods={"GET"})
     */
    public function testApiAction()
    {
        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param MovieManager $movieManager
     *
     * @return Response
     * @Route("/user/movie", name="post_user_movie", methods={"POST"})
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function postUserMovieAction(
        Request $request,
        EntityManagerInterface $entityManager,
        MovieManager $movieManager
    ) {
        if (null === ($imdbId = $request->request->get('imdbid', null))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Please provide IMDB ID',
                'id'      => $this->getUser()->getId()
            ]);
        }

        $userWithMovie = $entityManager
            ->getRepository(User::class)
            ->findMovieWithUser($user = $this->getUser(), $imdbId);

        if ($userWithMovie) {
            return new JsonResponse([
                'success' => false,
                'message' => 'User already has added this movie'
            ]);
        }

        $movie = $movieManager->getMovie($imdbId);

        if (!$movie) {
            return new JsonResponse([
                'success' => false,
                'message' => 'imdb id not found'
            ]);
        }

        /** @var User $user */
        $user->addMovie($movie);
        $entityManager->persist($movie);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Movie correctly added'
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param MovieManager $movieManager
     *
     * @return Response
     * @Route("/user/movie", name="delete_user_movie", methods={"DELETE"})
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteUsersMoviesAction(
        Request $request,
        EntityManagerInterface $entityManager,
        MovieManager $movieManager
    ) {
        if (null === ($imdbId = $request->request->get('imdbid', null))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Please provide IMDB ID',
                'id'      => $this->getUser()->getId()
            ]);
        }

        $userWithMovie = $entityManager
            ->getRepository(User::class)
            ->findMovieWithUser($user = $this->getUser(), $imdbId);

        if (!$userWithMovie) {
            return new JsonResponse([
                'success' => false,
                'message' => 'User doesnt have this movie'
            ]);
        }

        $movie = $movieManager->getMovie($imdbId, false);

        /** @var User $user */
        $user = $this->getUser();
        $user->removeMovie($movie);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Movie correctly removed from user'
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/user/movies", name="get_user_movies", methods={"GET"})
     */
    public function getUserMoviesAction(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        /** @var User $user */
        $user = $entityManager->getRepository(User::class)
            ->getUserWithMovies($this->getUser());

        $moviesAsJson = $serializer->serialize($user->getMovies()->toArray(), 'json');

        return new JsonResponse(
            $moviesAsJson,
            200,
            [],
            true
        );
    }

    /**
     * @param Movie $movie
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     *
     * @return JsonResponse
     * @Route("/movie/{imdbID}/users", name="get_movie_users", methods={"GET"})
     */
    public function getMovieUsersAction(
        Movie $movie,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        /** @var User[] $users */
        $users = $entityManager->getRepository(User::class)
            ->getUsersFromMovie($movie);

        $usersAsJson = $serializer->serialize($users, 'json');

        return new JsonResponse(
            $usersAsJson,
            200,
            [],
            true
        );
    }
}
