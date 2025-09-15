<?php

namespace App\Tests\Helper;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * Base test case with Factory support (FactoryBot-like).
 *
 * Extend this class in your tests to use factories:
 *   - use self::bootKernel();
 *   - then use YourEntityFactory::createOne([...])
 */
abstract class FoundryTestCase extends KernelTestCase
{
    use Factories;

    // Uncomment the next line if you want to reset the database between tests
    // use ResetDatabase;
}
