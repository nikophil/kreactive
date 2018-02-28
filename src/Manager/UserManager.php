<?php

namespace App\Manager;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class UserManager
 * @package AppBundle\Manager
 */
class UserManager
{
    /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface */
    private $encoderFactory;

    /** @var \Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface */
    private $tokenStorage;

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * UserManager constructor.
     *
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->encoderFactory  = $encoderFactory;
        $this->tokenStorage    = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \App\Entity\User $user
     *
     * @return \App\Entity\User
     * @throws \Exception
     */
    public function updatePassword(User $user)
    {
        if ($user->getPlainPassword()) {
            $user->setSalt($salt = base64_encode(random_bytes(24)));
            $user->setPassword($this->generatePassword($user, $user->getPlainPassword(), $salt));
            $user->eraseCredentials();
        }

        return $user;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword(UserInterface $user, string $password)
    {
        return $user->getPassword() === $this->generatePassword($user, $password, $user->getSalt());
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function loginUser(UserInterface $user, Request $request)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'public', $user->getRoles());
        $this->tokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch('security.interactive_login', $event);
    }

    /**
     * @param string|\Symfony\Component\Security\Core\User\UserInterface $user
     * @param string $plainPassword
     * @param string $salt
     *
     * @return string
     */
    private function generatePassword(UserInterface $user, string $plainPassword, string $salt)
    {
        return $this->encoderFactory->getEncoder($user)->encodePassword($plainPassword, $salt);
    }

    /**
     * @param Request $request
     *
     * @return User
     * @throws \Exception
     */
    public function createUser(Request $request)
    {
        $user = (new User())
            ->setFirstName($request->request->get('firstName'))
            ->setLastName($request->request->get('lastName'))
            ->setPlainPassword($request->request->get('plainPassword'))
            ->setEmail($request->request->get('email'));

        return $user;
    }
}
