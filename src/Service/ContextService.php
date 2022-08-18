<?php
// wrapper around a public class that can be used from twig or php

namespace Survos\BootstrapBundle\Service;


class ContextService
{
    const THEME_COLORS = [
        "primary",
        "secondary",
        "success",
        "info",
        "warning",
        "danger",
        "light",
        "dark"];

    public function __construct(array $options = [])
    {

    }


}
