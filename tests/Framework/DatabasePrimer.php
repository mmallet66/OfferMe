<?php

namespace App\Tests\Framework;

use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use Symfony\Component\HttpKernel\KernelInterface;

class DatabasePrimer
{
    public static function prime(KernelInterface $kernel)
    {
        if ('test' !== $kernel->getEnvironment()) {
            throw new LogicException("Primer must be executed in the test environment");            
        }

        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);
        $schemaTool->updateSchema($metadatas);
    }
}