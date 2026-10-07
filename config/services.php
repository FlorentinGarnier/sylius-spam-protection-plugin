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

use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\ContactTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\CustomerRegistrationTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\LiveComponentRenderTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\UserRequestPasswordResetTypeExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    foreach ([ContactTypeExtension::class, CustomerRegistrationTypeExtension::class, UserRequestPasswordResetTypeExtension::class] as $extension) {
        $services->set($extension)->tag('form.type_extension');
    }

    $services->set(LiveComponentRenderTypeExtension::class)->args([service('request_stack')])->tag('form.type_extension');
};
