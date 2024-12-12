<?php

namespace Survos\BootstrapBundle\Tests\src\Entity;

use Survos\BootstrapBundle\Traits\FacebookTrait;
use Survos\BootstrapBundle\Traits\GithubTrait;
use Survos\BootstrapBundle\Traits\GoogleTrait;
use Survos\BootstrapBundle\Traits\OAuthTrait;

class TestEntity
{
    use OAuthTrait;
    use GoogleTrait;
    use FacebookTrait;
    use GitHubTrait;

}
