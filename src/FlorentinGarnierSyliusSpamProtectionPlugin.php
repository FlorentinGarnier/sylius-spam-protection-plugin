<?php

/*
 * This file is part of the florentingarnier/sylius-spam-protection-plugin package.
 *
 * (c) Florentin Garnier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FlorentinGarnier\SyliusSpamProtectionPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class FlorentinGarnierSyliusSpamProtectionPlugin extends Bundle
{
    use SyliusPluginTrait;

    /**
     * The plugin lives at the package root (config/, templates/), not in src/.
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
