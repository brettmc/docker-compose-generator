<?php
namespace dcgen\Test;

use dcgen\Command\GenerateCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateTest extends TestCase
{
    private $command;
    private $commandTester;

    public function setup()
    {
        $this->command = new GenerateCommand();
        $this->commandTester = new CommandTester($this->command);
    }

    public function tearDown()
    {
        putenv('BAR=');
    }

    public function testBasicFunctionality()
    {
        $this->commandTester->execute([
            'template' => __DIR__.'/input/template.yml',
            '--env' => [
                'FOO=foo',
                'BAR=bar',
                'BAZ=baz',
            ],
        ]);
        $output = $this->commandTester->getDisplay();
        $expected = file_get_contents(__DIR__.'/output/output.yml');
        $this->assertEquals($expected, $output);
    }

    /**
     * @dataProvider excludeProvider
     */
    public function testExclude(array $exclude, string $file)
    {
        $this->commandTester->execute([
            'template' => __DIR__.'/input/template.yml',
            '--env' => [
                'FOO=foo',
                'BAR=bar',
                'BAZ=baz',
            ],
            '--exclude' => $exclude
        ]);
        $output = $this->commandTester->getDisplay();
        $expected = file_get_contents(__DIR__.'/output/'.$file);
        $this->assertEquals($expected, $output);
    }

    public function excludeProvider()
    {
        return [
            'exclude ports' => [
                ['ports'],
                'output-sans-ports.yml',
            ],
            'exclude labels' => [
                ['labels'],
                'output-sans-labels.yml',
            ],
        ];
    }

    public function testUsesEnvironmentFile()
    {
        $this->commandTester->execute([
            'template' => __DIR__.'/input/template.yml',
            '--env-file' => __DIR__.'/input/env.ini',
        ]);
        $output = $this->commandTester->getDisplay();
        $expected = file_get_contents(__DIR__.'/output/output.yml');
        $this->assertEquals($expected, $output);
    }

    public function testUsesEnvVariablesIfAvailable()
    {
        putenv('BAR=bar');
        $this->commandTester->execute([
            'template' => __DIR__.'/input/template.yml',
            '--env' => [
                'FOO=foo',
                'BAZ=baz',
            ],
        ]);
        $output = $this->commandTester->getDisplay();
        $expected = file_get_contents(__DIR__.'/output/output.yml');
        $this->assertEquals($expected, $output);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionIfInputFileNotFound()
    {
        $this->commandTester->execute([
            'template' => '/file/that/does/not/exist',
        ]);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsExceptionIfNoInputProvided()
    {
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        echo $output;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionIfIniFileNotFound()
    {
        $this->commandTester->execute([
            '--env-file' => '/file/that/does/not/exist',
        ]);
    }

    public function testReadsTemplateFromStdin()
    {
        $this->markTestSkipped('todo, how to use stdin as a command input stream');
        $template = file_get_contents(__DIR__.'/input/template.yml');
        //$this->commandTester->setInputs([$template]);
        $x = fwrite(STDIN, $template);
        $z = ftell(STDIN);
        $y = fseek(STDIN, 1-$x, SEEK_END);
        //$y = rewind(STDIN);
        $this->commandTester->execute([
            '--env' => [
                'FOO=foo',
                'BAR=bar',
                'BAZ=baz',
            ],
        ]);
        $output = $this->commandTester->getDisplay();
        $expected = file_get_contents(__DIR__.'/output/output.yml');
        $this->assertEquals($expected, $output);
    }

    public function testMissesAreWrittenToStdout()
    {
        $this->commandTester->execute([
            'template' => __DIR__.'/input/template.yml',
            '--env' => [
                'FOO=foo',
            ],
        ]);
        $output = $this->commandTester->getDisplay();
        $this->assertContains('WARNING: 2 keys were not defined: BAR, BAZ', $output);
    }
}
