<?php

namespace Survos\BaseBundle\Traits;

trait GoogleTrait
{
    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $googleId = null;

    /**
     * @return string
     */
    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    /**
     * @param string $googleId
     * @return self
     */
    public function setGoogleId(?string $googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

}
