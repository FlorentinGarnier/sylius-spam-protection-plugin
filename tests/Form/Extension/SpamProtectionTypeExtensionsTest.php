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

namespace FlorentinGarnier\SyliusSpamProtectionPlugin\Tests\Form\Extension;

use FlorentinGarnier\SpamProtection\IpReputation\IpReputation;
use FlorentinGarnier\SpamProtection\IpReputation\IpReputationList;
use FlorentinGarnier\SpamProtection\SpamProtection;
use FlorentinGarnier\SpamProtectionBundle\Form\SpamProtectionType;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\ContactTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\CustomerRegistrationTypeExtension;
use FlorentinGarnier\SyliusSpamProtectionPlugin\Form\Extension\UserRequestPasswordResetTypeExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylius\Bundle\CoreBundle\Form\Type\ContactType;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerRegistrationType;
use Sylius\Bundle\UserBundle\Form\Type\UserRequestPasswordResetType;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\RequestStack;

final class SpamProtectionTypeExtensionsTest extends TypeTestCase
{
    /**
     * @return iterable<string, array{AbstractTypeExtension, class-string, string, list<string>}>
     */
    public static function protectedForms(): iterable
    {
        yield 'contact' => [new ContactTypeExtension(), ContactType::class, 'contact', ['message']];
        yield 'registration' => [new CustomerRegistrationTypeExtension(), CustomerRegistrationType::class, 'registration', []];
        yield 'password reset' => [new UserRequestPasswordResetTypeExtension(), UserRequestPasswordResetType::class, 'password_reset', []];
    }

    /**
     * @param class-string $extendedType
     * @param list<string> $contentFields
     */
    #[DataProvider('protectedForms')]
    public function testItAddsTheSpamProtectionToTheSyliusForm(AbstractTypeExtension $extension, string $extendedType, string $scope, array $contentFields): void
    {
        $builder = $this->factory->createBuilder();

        $extension->buildForm($builder, []);

        $field = $builder->getForm()->get('spam_protection')->getConfig();
        self::assertSame([$extendedType], [...$extension::getExtendedTypes()]);
        self::assertInstanceOf(SpamProtectionType::class, $field->getType()->getInnerType());
        self::assertSame($scope, $field->getOption('protection_scope'));
        self::assertSame($contentFields, $field->getOption('content_fields'));
    }

    protected function getExtensions(): array
    {
        $ipReputation = new IpReputation(new IpReputationList(sys_get_temp_dir() . '/missing_ip_reputation_list.php'));

        return [new PreloadedExtension([
            new SpamProtectionType(SpamProtection::create('secret', new ArrayAdapter(), $ipReputation), new RequestStack(), 'secret'),
        ], [])];
    }
}
