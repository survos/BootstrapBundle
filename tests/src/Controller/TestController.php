<?php

namespace Survos\BootstrapBundle\Tests\src\Controller;

use Survos\BootstrapBundle\Traits\CreateFromTrait;
use Survos\BootstrapBundle\Traits\JsonResponseTrait;

class TestController
{
    use JsonResponseTrait;
    use CreateFromTrait;

    public function __construct()
    {

    }

}
