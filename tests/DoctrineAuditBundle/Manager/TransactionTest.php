<?php

namespace DH\DoctrineAuditBundle\Tests\Manager;

use DH\DoctrineAuditBundle\Annotation\AnnotationLoader;
use DH\DoctrineAuditBundle\Configuration;
use DH\DoctrineAuditBundle\Helper\AuditHelper;
use DH\DoctrineAuditBundle\Manager\Transaction;
use DH\DoctrineAuditBundle\Tests\BaseTest;
use DH\DoctrineAuditBundle\User\TokenStorageUserProvider;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

/**
 * @internal
 */
final class TransactionTest extends BaseTest
{
    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    public function testGetTransactionHash(): void
    {
        $configuration = $this->getAuditConfiguration();
        $helper = new AuditHelper($configuration);
        $transaction = new Transaction($helper);

        $transaction_hash = $transaction->getTransactionHash();
        self::assertNotNull($transaction_hash, 'transaction_hash is not null');
        self::assertIsString($transaction_hash, 'transaction_hash is a string');
        self::assertSame(40, mb_strlen($transaction_hash), 'transaction_hash is a string of 40 characters');
    }

    protected function getAuditConfiguration(array $options = [], ?EntityManager $entityManager = null): Configuration
    {
        $container = new ContainerBuilder();
        $em = $entityManager ?? $this->getEntityManager();

        return new Configuration(
            array_merge([
                'table_prefix' => '',
                'table_suffix' => '_audit',
                'timezone' => 'UTC',
                'ignored_columns' => [],
                'entities' => [],
                'enabled' => true,
                'enabled_viewer' => true,
            ], $options),
            new TokenStorageUserProvider(new Security($container)),
            new RequestStack(),
            new FirewallMap($container, []),
            $em,
            new AnnotationLoader($em),
            new EventDispatcher()
        );
    }
}
