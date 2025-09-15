<?php

namespace App\Tests;

use App\Tests\Helper\FoundryTestCase;

use function Zenstruck\Foundry\faker;

class FactorySmokeTest extends FoundryTestCase
{
    public function testFakerIsAvailable()
    {
        $name = faker()->name();
        $this->assertNotEmpty($name);
    }
}
