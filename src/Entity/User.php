<?php

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Jms;

/**
 * Class User
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 * @Jms\ExclusionPolicy("all")
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    use IdTrait;

    /**
     * @var ArrayCollection|Movie[]
     *
     * @ORM\ManyToMany(targetEntity="Movie", cascade={"persist", "merge"}, inversedBy="users")
     * @ORM\JoinTable(name="users_movies",
     *     joinColumns={@ORM\JoinColumn(name="id_user", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="id_movie", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $movies;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=127)
     * @Assert\NotBlank()
     * @Assert\Email(checkMX=true)
     *
     * @Jms\Expose
     * @Jms\SerializedName("email")
     * @Jms\Type("string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=63)
     * @Assert\NotBlank()
     *
     * @Jms\Expose
     * @Jms\SerializedName("firstName")
     * @Jms\Type("string")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=63)
     * @Assert\NotBlank()
     *
     * @Jms\Expose
     * @Jms\SerializedName("lastName")
     * @Jms\Type("string")
     */
    protected $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(
            [
                $this->id,
                $this->email,
                $this->password,
                $this->salt,
            ]
        );
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            $this->salt,
            ) = unserialize($serialized);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     *
     * @return $this
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @return $this
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     *
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
        return $this;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user = null)
    {
        return $user && $this->getUsername() === $user->getUsername();
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function updateCreatedDate()
    {
        $this->created = new \DateTime();
    }

    /**
     * @return Movie[]|Collection
     */
    public function getMovies()
    {
        return $this->movies;
    }

    /**
     * @param Movie[]|Collection $movies
     *
     * @return $this
     */
    public function setMovies($movies)
    {
        $this->movies = $movies;
        return $this;
    }

    /**
     * @param Movie $movie
     *
     * @return $this
     */
    public function addMovie(Movie $movie)
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
            $movie->addUser($this);
        }

        return $this;
    }

    /**
     * @param Movie $movie
     *
     * @return $this
     */
    public function removeMovie(Movie $movie)
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
            $movie->removeUser($this);
        }

        return $this;
    }
}
