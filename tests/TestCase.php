<?php

namespace F9Web\ApiResponseHelpers\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public static $latestResponse;

    public function setUp(): void
    {
        parent::setUp();
    }
}
