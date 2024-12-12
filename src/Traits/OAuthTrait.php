<?php

namespace Survos\BootstrapBundle\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

trait OAuthTrait
{
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $authKeys = null;
    public function getAuthKeys(): ?array
    {
        return $this->authKeys;
    }

    public function setAuthKeys(array $authKeys): self
    {
        $this->authKeys = $authKeys;
        return $this;
    }

}
