<?php

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * Class Movie
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
{
    use IdTrait;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", cascade={"persist", "merge"}, mappedBy="movies")
     */
    private $users;

    /**
     * @var string
     * @ORM\Column(type="string")
     *
     * @Jms\SerializedName("imdbID")
     * @Jms\Type("string")
     */
    private $imdbID;

    /**
     * @var string
     * @ORM\Column(type="string")
     *
     * @Jms\SerializedName("title")
     * @Jms\Type("string")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string")
     *
     * @Jms\SerializedName("poster")
     * @Jms\Type("string")
     */
    private $poster;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getImdbID()
    {
        return $this->imdbID;
    }

    /**
     * @param string $imdbID
     *
     * @return $this
     */
    public function setImdbID($imdbID)
    {
        $this->imdbID = $imdbID;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param string $poster
     *
     * @return $this
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;
        return $this;
    }

    /**
     * @return User[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User[]|ArrayCollection $users
     *
     * @return $this
     */
    public function setUsers($users)
    {
        $this->users = $users;
        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function removeUser(User $user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }
}
