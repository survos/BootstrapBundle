<?php

namespace Survos\BootstrapBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

trait OAuthTrait
{
    /**
     * @ORM\Column(type="json", nullable=true, unique=true)
     */
    private $authKeys = [];

    public function getGithubId(): ?int
    {
        return $this->githubId;
    }

    public function setGithubId(?int $githubId): self
    {
        $this->githubId = $githubId;

        return $this;
    }
}
