<?php

namespace Survos\BootstrapBundle\Model;

class Tab
{
    public function __construct(
        public ?string $title = null,
        public ?string $content = null,
        public ?string $id=null,
        public ?string $translationDomain=null
    ) {
        if (empty($this->id)) {
            $this->id = md5($this->title); // slugger would be better!
        }
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getLabel(): string
    {
        return $this->title;
    }
}
