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

namespace Tests\FlorentinGarnier\SyliusSpamProtectionPlugin\Unit\DependencyInjection;

use FlorentinGarnier\SyliusSpamProtectionPlugin\DependencyInjection\FlorentinGarnierSyliusSpamProtectionExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\ContactTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\CustomerRegistrationTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\LiveComponentRenderTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\UserRequestPasswordResetTypeExtension;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FlorentinGarnierSyliusSpamProtectionExtensionTest extends TestCase
{
    private const TEMPLATE = '@FlorentinGarnierSyliusSpamProtectionPlugin/spam_protection.html.twig';

    private const ERROR_TEMPLATE = '@FlorentinGarnierSyliusSpamProtectionPlugin/spam_protection_error.html.twig';

    public function testItRegistersTheFormTypeExtensions(): void
    {
        $container = new ContainerBuilder();

        (new FlorentinGarnierSyliusSpamProtectionExtension())->load([], $container);

        foreach ([ContactTypeExtension::class, CustomerRegistrationTypeExtension::class, UserRequestPasswordResetTypeExtension::class, LiveComponentRenderTypeExtension::class] as $extension) {
            self::assertTrue($container->getDefinition($extension)->hasTag('form.type_extension'), $extension);
        }
    }

    /**
     * Sylius 1 shop templates end their forms with render_rest set to false: the field must be rendered by a template event.
     */
    public function testItRendersTheProtectionInTheFormTemplateEventsOfTheShop(): void
    {
        if ('1' !== SyliusCoreBundle::MAJOR_VERSION) {
            self::markTestSkipped('Sylius 1 only.');
        }

        $container = new ContainerBuilder();

        (new FlorentinGarnierSyliusSpamProtectionExtension())->prepend($container);

        $config = $container->getExtensionConfig('sylius_ui');
        foreach (['sylius.shop.contact.request.form', 'sylius.shop.register.form', 'sylius.shop.request_password_reset_token.form'] as $event) {
            self::assertSame(self::TEMPLATE, self::get($config, 0, 'events', $event, 'blocks', 'florentin_garnier_spam_protection', 'template'), $event);
            self::assertSame(self::ERROR_TEMPLATE, self::get($config, 0, 'events', $event, 'blocks', 'florentin_garnier_spam_protection_error', 'template'), $event);
        }
    }

    /**
     * Sylius 2 shop templates end their forms with render_rest set to false: the field must be rendered by a Twig hook.
     */
    public function testItRendersTheProtectionInTheFormHooksOfTheShop(): void
    {
        if ('1' === SyliusCoreBundle::MAJOR_VERSION) {
            self::markTestSkipped('Sylius 2 only.');
        }

        $container = new ContainerBuilder();

        (new FlorentinGarnierSyliusSpamProtectionExtension())->prepend($container);

        $config = $container->getExtensionConfig('sylius_twig_hooks');
        foreach (['sylius_shop.contact.contact_request.content.form', 'sylius_shop.account.register.content.form', 'sylius_shop.account.forgotten_password.content.form_container.form'] as $hook) {
            self::assertSame(self::TEMPLATE, self::get($config, 0, 'hooks', $hook, 'florentin_garnier_spam_protection', 'template'), $hook);
            self::assertSame(self::ERROR_TEMPLATE, self::get($config, 0, 'hooks', $hook, 'florentin_garnier_spam_protection_error', 'template'), $hook);
        }
        self::assertSame([], $container->getExtensionConfig('sylius_ui'));
    }

    private static function get(mixed $config, int|string ...$path): mixed
    {
        foreach ($path as $key) {
            self::assertIsArray($config);
            self::assertArrayHasKey($key, $config);
            $config = $config[$key];
        }

        return $config;
    }
}
