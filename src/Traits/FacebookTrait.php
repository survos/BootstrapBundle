<?php

namespace Survos\BootstrapBundle\Traits;

trait FacebookTrait
{
    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $facebookId = null;

    /**
     * @return string
     */
    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookId
     * @return self
     */
    public function setFacebookId(?string $facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }
}
