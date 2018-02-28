<?php

namespace App\DataFixtures\ORM;

use App\Entity\Movie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class MovieFixtures
 */
class MovieFixtures extends Fixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     *
     * @throws \Exception
     * @throws \Doctrine\Common\DataFixtures\BadMethodCallException
     */
    public function load(ObjectManager $manager)
    {
        $movies = '[
    {
        "Title": "Pirate Radio",
      "Year": "2009",
      "imdbID": "tt1131729",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMTMzMjYzMTMyM15BMl5BanBnXkFtZTcwOTk5NDA5Mg@@._V1_SX300.jpg"
    },
    {
        "Title": "TPB AFK: The Pirate Bay Away from Keyboard",
      "Year": "2013",
      "imdbID": "tt2608732",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMjU4MzY4ODg0MV5BMl5BanBnXkFtZTcwNTE2MjY5OA@@._V1_SX300.jpg"
    },
    {
        "Title": "The Pirate Fairy",
      "Year": "2014",
      "imdbID": "tt2483260",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMTk5NjUzMDg3OV5BMl5BanBnXkFtZTgwMDE1MzYwMTE@._V1_SX300.jpg"
    },
    {
        "Title": "Harlock: Space Pirate",
      "Year": "2013",
      "imdbID": "tt2668134",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMzY3ZDNhZjgtYzVhYy00MTgwLWJjYjgtMjQwZGFjNGU1MTBjXkEyXkFqcGdeQXVyNTAyODkwOQ@@._V1_SX300.jpg"
    },
    {
        "Title": "The Crimson Pirate",
      "Year": "1952",
      "imdbID": "tt0044517",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BYTVjMDM2NGMtYjE0Ny00MTQ5LTg4MmUtNDU0ZGY3NGQ0OTA3XkEyXkFqcGdeQXVyMDI2NDg0NQ@@._V1_SX300.jpg"
    },
    {
        "Title": "The Pirate",
      "Year": "1948",
      "imdbID": "tt0040694",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMTY4NzU3NDI2NV5BMl5BanBnXkFtZTgwMDY0OTgxMTE@._V1_SX300.jpg"
    },
    {
        "Title": "The Pirate Movie",
      "Year": "1982",
      "imdbID": "tt0084504",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMjAwNjIxMjAyNV5BMl5BanBnXkFtZTcwNjA0MDgyMQ@@._V1_SX300.jpg"
    },
    {
        "Title": "The Princess and the Pirate",
      "Year": "1944",
      "imdbID": "tt0037193",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMTg1NzU4NDI5N15BMl5BanBnXkFtZTcwODkwNjgyMQ@@._V1_SX300.jpg"
    },
    {
        "Title": "The Black Pirate",
      "Year": "1926",
      "imdbID": "tt0016654",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMzU0NDkyMjEzMV5BMl5BanBnXkFtZTgwMTcyMzEyMjE@._V1_SX300.jpg"
    },
    {
        "Title": "Blackie the Pirate",
      "Year": "1971",
      "imdbID": "tt0066950",
      "Type": "movie",
      "Poster": "https://images-na.ssl-images-amazon.com/images/M/MV5BMTc1ODU2NDQ1Nl5BMl5BanBnXkFtZTcwNDczMzMzMQ@@._V1_SX300.jpg"
    }
  ]';
        $movies = json_decode($movies, true);

        $i = 0;
        foreach ($movies as $movieData) {
            /** @var Movie $movie */
            $movie = (new Movie())
                ->setTitle($movieData['Title'])
                ->setPoster($movieData['Poster'])
                ->setImdbID($movieData['imdbID'])
            ;

            $this->addReference('movie-' . $i++, $movie);

            $manager->persist($movie);
        }

        $manager->flush();

        return;
    }
}
