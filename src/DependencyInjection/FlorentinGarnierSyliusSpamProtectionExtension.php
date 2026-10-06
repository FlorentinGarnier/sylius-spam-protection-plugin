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

namespace FlorentinGarnier\SyliusSpamProtectionPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class FlorentinGarnierSyliusSpamProtectionExtension extends Extension implements PrependExtensionInterface
{
    private const FORM_TEMPLATE_EVENTS = [
        'sylius.shop.contact.request.form',
        'sylius.shop.register.form',
        'sylius.shop.request_password_reset_token.form',
    ];

    public function load(array $configs, ContainerBuilder $container): void
    {
        (new PhpFileLoader($container, new FileLocator(\dirname(__DIR__, 2) . '/config')))->load('services.php');
    }

    /**
     * Shop templates end their forms with render_rest set to false, so the protection is rendered through their template events.
     * A theme that already renders the field explicitly is unaffected: the template skips a field that is already rendered.
     */
    public function prepend(ContainerBuilder $container): void
    {
        $block = ['template' => '@FlorentinGarnierSyliusSpamProtectionPlugin/spam_protection.html.twig', 'priority' => -100];

        $container->prependExtensionConfig('sylius_ui', [
            'events' => array_fill_keys(self::FORM_TEMPLATE_EVENTS, ['blocks' => ['florentin_garnier_spam_protection' => $block]]),
        ]);
    }
}
