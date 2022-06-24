<?php


namespace Survos\BaseBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserCreatedEvent extends Event
{

    private ?string $extra;
    private UserInterface $user;

    public function getExtra(): ?string
    {
        return $this->extra;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function __construct(UserInterface $user, ?string $extra=null) {
        $this->extra = $extra;
        $this->user = $user;
    }


}

