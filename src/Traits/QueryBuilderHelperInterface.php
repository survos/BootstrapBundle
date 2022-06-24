<?php

namespace Survos\BaseBundle\Traits;

interface QueryBuilderHelperInterface
{

    public function getCounts($field): array;
    public function findBygetCountsByField($field = 'marking', $filters = []): array;

}
