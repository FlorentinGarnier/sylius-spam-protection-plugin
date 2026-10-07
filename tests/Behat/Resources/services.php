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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Tests\FlorentinGarnier\SyliusSpamProtectionPlugin\Behat\Context\SpamProtectionContext;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('florentin_garnier_spam_protection.behat.context.spam_protection', SpamProtectionContext::class)
            ->public()
            ->args([service('behat.mink.default_session'), service('cache.app')])
    ;
};
