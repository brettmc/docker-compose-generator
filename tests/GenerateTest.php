<?php
namespace dcgen\Test;

use dcgen\Command\GenerateCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateTest extends TestCase
{
    private $commandTester;
    private $arguments;

    public function setup(): void
    {
        $command = new GenerateCommand();
        $this->commandTester = new CommandTester($command);
        $this->arguments = [
            '--input' => [
                __DIR__.'/input/template.yml',
            ],
            '--env' => [
                'FOO=foo',
                'BAR=bar',
                'BAZ=baz',
            ],
        ];
    }

    public function tearDown(): void
    {
        putenv('BAR=');
    }

    public function testBasicFunctionality()
    {
        $this->commandTester->execute($this->arguments);
        $output = $this->commandTester->getDisplay();
        $expected = file_get_contents(__DIR__.'/output/output.yml');
        $this->assertEquals($expected, $output);
    }

    /**
     * @dataProvider excludeProvider
     */
    public function testExclude(array $exclude, string $file, string $fs = '.')
    {
        $this->commandTester->execute($this->arguments + [
            '--exclude' => $exclude,
            '--fs' => $fs,
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
            'exclude my-service:labels' => [
                ['my-service:labels'],
                'output-sans-labels.yml',
                ':',
            ],
            'exclude ^my-service:labels' => [
                ['^services:my-service:labels'],
                'output-sans-labels.yml',
                ':',
            ],
        ];
    }

    public function testUsesSettingsFromIniFiles()
    {
        $this->commandTester->execute([
            '--input' => [
                __DIR__.'/input/template.yml',
            ],
            '--ini' => [
                __DIR__.'/input/foo.ini',
                __DIR__.'/input/bar.ini',
            ],
            '--env' => [
                'BAZ=baz',
            ],
        ]);
        $output = $this->commandTester->getDisplay();
        $expected = file_get_contents(__DIR__.'/output/output.yml');
        $this->assertEquals($expected, $output);
    }

    public function testUsesEnvVariablesIfAvailable()
    {
        putenv('BAR=bar');
        $this->commandTester->execute([
            '--input' => [
                __DIR__.'/input/template.yml',
            ],
            '--env' => [
                'FOO=foo',
                'BAZ=baz',
            ],
        ]);
        $output = $this->commandTester->getDisplay();
        $expected = file_get_contents(__DIR__.'/output/output.yml');
        $this->assertEquals($expected, $output);
    }

    public function testThrowsExceptionIfInputFileNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->commandTester->execute([
            '--input' => [
                '/file/that/does/not/exist',
            ],
        ]);
    }

    public function testThrowsExceptionIfNoInputProvided()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->commandTester->execute([]);
    }

    public function testThrowsExceptionIfIniFileNotFound()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute([
            '--input' => [
                __DIR__.'/input/template.yml',
            ],
            '--ini' => [
                __DIR__.'/input/env.ini',
                '/file/that/does/not/exist',
            ],
        ]);
    }

    public function testReadsTemplateFromStdin()
    {
        $template = file_get_contents(__DIR__.'/input/template.yml');
        $this->commandTester->setInputs([$template]);
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

    public function testReadsTemplateFromInputOption()
    {
        $this->commandTester->execute([
            '--input' => [
                __DIR__.'/input/template.yml',
            ],
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
        $this->commandTester->execute(
            [
                '--input' => [
                    __DIR__.'/input/template.yml',
                ],
                '--env' => [
                    'FOO=foo',
                ],
            ],
            [
                'capture_stderr_separately' => true,
            ]
        );
        $output = $this->commandTester->getErrorOutput();
        $this->assertStringContainsString('WARNING: 2 keys were not defined: BAR, BAZ', $output);
    }
}
