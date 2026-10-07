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

use Behat\Behat\Context\Context;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    // Behat is removed from the CI jobs on the Symfony versions it does not support (5.4 and 8).
    if (str_starts_with($container->env() ?? '', 'test') && interface_exists(Context::class)) {
        // Sylius 2.3 ships its Behat services in PHP, earlier versions in XML (which Symfony 8 can no longer load).
        $syliusBehatServices = \dirname(__DIR__, 3) . '/vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services';
        $container->import(file_exists($syliusBehatServices . '.php') ? $syliusBehatServices . '.php' : $syliusBehatServices . '.xml');
        $container->import('@FlorentinGarnierSyliusSpamProtectionPlugin/tests/Behat/Resources/services.php');
    }
};
