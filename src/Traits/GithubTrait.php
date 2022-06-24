<?php

namespace Survos\BaseBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

trait GithubTrait
{
    /**
     * @ORM\Column(type="integer", nullable=true, unique=true)
     */
    private $githubId = null;

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
