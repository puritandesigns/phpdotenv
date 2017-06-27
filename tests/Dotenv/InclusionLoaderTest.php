<?php

use PHPUnit\Framework\TestCase;
use Dotenv\Loader\InclusionLoader;

class InclusionLoaderTest extends TestCase
{
    /** @var \Dotenv\Loader\InclusionLoader */
    protected $loader;

    protected function setUpLoader()
    {
        $this->loader = new InclusionLoader(
            __DIR__ . '/../fixtures/env-inclusion/.env',
            __DIR__ . '/../fixtures/env-inclusion/include.env',
            true
        );
    }

    public function testInstantiation()
    {
        $this->setUpLoader();
        $this->assertNotEmpty($_SERVER['ANOTHER']);
        $this->assertTrue(empty($_SERVER['TESTING']));
    }

    public function testLoad()
    {
        $this->setUpLoader();
        $this->loader->load();

        $this->assertNotEmpty($_SERVER['ANOTHER']);
        $this->assertNotEmpty($_SERVER['TESTING']);
        $this->assertNotEmpty($_SERVER['TEST2']);
        $this->assertNotEmpty($_SERVER['STRING']);
        $this->assertTrue(empty($_SERVER['TEST1']));
    }

    public function testEmptyInclusionLoad()
    {
        $loader = new InclusionLoader(__DIR__ . '/../fixtures/env-inclusion/.env');
        $loader->load();

        $this->assertEquals(123, $_ENV['TESTING']);
        $this->assertEquals(1, $_ENV['TEST1']);
        $this->assertEquals(2, $_ENV['TEST2']);
        $this->assertEquals('loremipsum', $_ENV['STRING']);
    }

    public function testCompareFilesReturnsNoCount()
    {
        $loader = new InclusionLoader(null);

        $diff = $loader->compareEnvFiles(
            __DIR__ . '/../fixtures/env-inclusion/include.env',
            __DIR__ . '/../fixtures/env-inclusion/compare1.env'
        );

        $this->assertEquals(0, count($diff));

        $diff = $loader->compareEnvFiles(
            __DIR__ . '/../fixtures/env-inclusion/include.env',
            __DIR__ . '/../fixtures/env-inclusion/compare2.env'
        );

        $this->assertEquals(1, count($diff));
    }
}
