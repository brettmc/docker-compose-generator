<?php
namespace dcgen\Test;

use dcgen\EnvironmentLoader;
use PHPUnit\Framework\TestCase;

class EnvironmentLoaderTest extends TestCase
{
    public function testLaterSettingsOverrideEarlier()
    {
        $filenames = [
            __DIR__.'/input/env.ini',
            __DIR__.'/input/bar.override.ini',
        ];
        $loader = new EnvironmentLoader();
        $loader->load($filenames);
        $expected = [
            'FOO' => 'foo',
            'BAR' => 'elephant',
            'BAZ' => 'baz',
        ];
        $this->assertEquals($expected, $loader->get());

        $loader->add(['BAR' => 'kangaroo']);
        $expected['BAR'] = 'kangaroo';
        $this->assertEquals($expected, $loader->get());
    }

    public function testThrowsExceptionOnNotFoundIniFile()
    {
        $this->expectException(\RuntimeException::class);
        $filenames = [
            __DIR__.'/input/does-not-exist.ini',
        ];
        $loader = new EnvironmentLoader();
        $loader->load($filenames);
    }
}
