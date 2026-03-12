<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Forzar MySQL para tests
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'fonrio_test']);
    }
}