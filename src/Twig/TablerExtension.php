<?php

/*
 * This file is part of the Tabler bundle, created by Kevin Papst (www.kevinpapst.de).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Survos\BootstrapBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TablerExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('tabler_container', [TablerRuntimeExtension::class, 'containerClass']),
            new TwigFilter('tabler_body', [TablerRuntimeExtension::class, 'bodyClass']),
            new TwigFilter('tabler_route', [TablerRuntimeExtension::class, 'getRouteByAlias']),
            new TwigFilter('tabler_icon', [TablerRuntimeExtension::class, 'icon']),
        ];
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('tabler_asset_version', [TablerRuntimeExtension::class, 'assetVersion']),
            new TwigFunction('tabler_icon', [TablerRuntimeExtension::class, 'createIcon'], ['is_safe' => ['html']]),
            new TwigFunction('tabler_menu', [TablerRuntimeExtension::class, 'getMenu']),
            new TwigFunction('tabler_notifications', [TablerRuntimeExtension::class, 'getNotifications']),
            new TwigFunction('tabler_theme', [TablerRuntimeExtension::class, 'theme']),
            new TwigFunction('tabler_unique_id', [TablerRuntimeExtension::class, 'uniqueId']),
            new TwigFunction('tabler_user', [TablerRuntimeExtension::class, 'getUserDetails']),
        ];
    }
}
