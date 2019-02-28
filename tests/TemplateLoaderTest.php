<?php
namespace dcgen\Test;

use dcgen\TemplateLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\Input;

class TemplateLoaderTest extends TestCase
{
    private $loader;
    private $input;

    public function setup()
    {
        $this->input = $this->getMockBuilder(Input::class)->disableOriginalConstructor()->getMock();
        $this->loader = new TemplateLoader();
    }

    public function testLoadsSingleInputFile()
    {
        $filenames = [
            __DIR__.'/input/template.yml',
        ];
        $output = $this->loader->load($this->input, $filenames);
        $this->assertInternalType('array', $output);
        $this->assertEquals(include(__DIR__.'/output/template.php'), $output);
    }

    public function testLoadsAndMergesMultipleInputFiles()
    {
        $filenames = [
            __DIR__.'/input/template.yml',
            __DIR__.'/input/template2.yml',
            __DIR__.'/input/template3.yml',
        ];
        $output = $this->loader->load($this->input, $filenames);
        $this->assertInternalType('array', $output);
        $this->assertEquals(include(__DIR__.'/output/templates-merged.php'), $output);
    }

    public function testFileInputOverridesStreamInput()
    {
        $stream = fopen(__DIR__.'/input/template3.yml', 'r');
        $this->input->method('getStream')->willReturn($stream);
        $filenames = [
            __DIR__.'/input/template4.yml',
        ];
        $output = $this->loader->load($this->input, $filenames);
        $this->assertInternalType('array', $output);
        $this->assertEquals(include(__DIR__.'/output/templates-34-merged.php'), $output);
    }

    public function testFileInputsOverridesPreviousFileInput()
    {
        $filenames = [
            __DIR__.'/input/template3.yml',
            __DIR__.'/input/template4.yml',
        ];
        $output = $this->loader->load($this->input, $filenames);
        $this->assertInternalType('array', $output);
        $this->assertEquals(include(__DIR__.'/output/templates-34-merged.php'), $output);
    }
}
