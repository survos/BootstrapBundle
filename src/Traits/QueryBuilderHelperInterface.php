<?php

namespace Survos\BootstrapBundle\Traits;

interface QueryBuilderHelperInterface
{
    public function getCounts($field): array;

    public function findBygetCountsByField($field = 'marking', $filters = []): array;
}
