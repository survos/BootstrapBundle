<?php

/*
 * This file is part of the AdminLTE bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Survos\BootstrapBundle\Event;

//use Survos\BootstrapBundle\Model\NavBarUserLink;
//use Survos\BootstrapBundle\Model\UserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Collect the UserInterface object that should be rendered in the user section.
 */
abstract class ShowUserEvent extends ThemeEvent
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var bool
     */
    private $showProfileLink = true;

    /**
     * @var bool
     */
    private $showLogoutLink = true;

    private $links = [];

    /**
     * @param UserInterface $user
     * @return ShowUserEvent
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }


    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @todo: should be NavBarUserLink $link
     * @return ShowUserEvent
     */
    public function addLink(string $link)
    {
        $this->links[] = $link;

        return $this;
    }


    public function isShowProfileLink(): bool
    {
        return $this->showProfileLink;
    }

    /**
     * @return ShowUserEvent
     */
    public function setShowProfileLink(bool $showProfileLink)
    {
        $this->showProfileLink = $showProfileLink;

        return $this;
    }


    public function isShowLogoutLink(): bool
    {
        return $this->showLogoutLink;
    }

    /**
     * @return ShowUserEvent
     */
    public function setShowLogoutLink(bool $showLogoutLink)
    {
        $this->showLogoutLink = $showLogoutLink;

        return $this;
    }
}
