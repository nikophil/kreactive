<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Guzzle\Client;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PublicApiController
 */
class PublicApiController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/test", name="test-public-api", methods={"GET"})
     */
    public function testApiAction()
    {
        return new JsonResponse([
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     * @param UserManager $userManager
     *
     * @param ValidatorInterface $validator
     *
     * @param EntityManagerInterface|EntityManager $entityManager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     * @Route("/user", name="post_user", methods={"POST"})
     */
    public function postUserAction(
        Request $request,
        UserManager $userManager,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ) {
        $user = $userManager->createUser($request);

        /** @var ConstraintViolationInterface[] $errors */
        if (count($errors = $validator->validate($user)) == 0) {
            $userManager->updatePassword($user);

            $entityManager->persist($user);
            $entityManager->flush($user);

            return new JsonResponse([
                'success' => true,
                'message' => 'User correctly created, you can now login'
            ]);
        }

        $serializedErrors = [];
        foreach ($errors as $error) {
            $serializedErrors[] = [
                'message'  => $error->getMessage(),
                'property' => $error->getPropertyPath()
            ];
        }

        return new JsonResponse([
            'success' => false,
            'errors'  => $serializedErrors
        ]);
    }

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     * @Route("/bestMovie", name="get_best_movie", methods={"GET"})
     */
    public function getBestMovieAction(EntityManagerInterface $entityManager)
    {
        $bestMovie = $entityManager->getRepository(Movie::class)->findBestMovie();

        return new JsonResponse([
            'nbUsers' => $bestMovie['nbUsers'],
            'imdbID'  => $bestMovie['imdbID'],
            'title'   => $bestMovie['title'],
        ]);
    }

    /**
     * @param Client $client
     *
     * @param int $page
     *
     * @return Response
     * @Route("/movies/{page}", name="get_movies", methods={"GET"}, defaults={"page": 1})
     */
    public function getMovies(Client $client, $page = 1)
    {
        return new JsonResponse(
            $client->request(['s' => 'pirate', 'page' => $page])->getBody(),
            200,
            [],
            true
        );
    }
}
