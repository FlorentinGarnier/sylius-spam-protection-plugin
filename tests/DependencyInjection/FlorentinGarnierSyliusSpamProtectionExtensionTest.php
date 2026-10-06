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

namespace FlorentinGarnier\SyliusSpamProtectionPlugin\Tests\DependencyInjection;

use FlorentinGarnier\SyliusSpamProtectionPlugin\DependencyInjection\FlorentinGarnierSyliusSpamProtectionExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\ContactTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\CustomerRegistrationTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\UserRequestPasswordResetTypeExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FlorentinGarnierSyliusSpamProtectionExtensionTest extends TestCase
{
    public function testItRegistersTheFormTypeExtensions(): void
    {
        $container = new ContainerBuilder();

        (new FlorentinGarnierSyliusSpamProtectionExtension())->load([], $container);

        foreach ([ContactTypeExtension::class, CustomerRegistrationTypeExtension::class, UserRequestPasswordResetTypeExtension::class] as $extension) {
            self::assertTrue($container->getDefinition($extension)->hasTag('form.type_extension'), $extension);
        }
    }

    /**
     * Sylius shop templates end their forms with render_rest set to false: the field must be rendered by a template event.
     */
    public function testItRendersTheProtectionInTheFormTemplateEventsOfTheShop(): void
    {
        $container = new ContainerBuilder();

        (new FlorentinGarnierSyliusSpamProtectionExtension())->prepend($container);

        $events = array_merge(...array_column($container->getExtensionConfig('sylius_ui'), 'events'));
        foreach (['sylius.shop.contact.request.form', 'sylius.shop.register.form', 'sylius.shop.request_password_reset_token.form'] as $event) {
            self::assertSame('@FlorentinGarnierSyliusSpamProtectionPlugin/spam_protection.html.twig', $events[$event]['blocks']['florentin_garnier_spam_protection']['template'], $event);
        }
    }
}
