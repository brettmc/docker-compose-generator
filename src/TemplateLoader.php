<?php
namespace dcgen;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

class TemplateLoader
{
    private $filename;

    public function __construct(string $filename = null)
    {
        if ($filename && !file_exists($filename)) {
            throw new \InvalidArgumentException('File not found: '.$filename);
        }
        $this->filename = $filename;
    }

    public function load(InputInterface $input): array
    {
        $content = $this->readFileOrStdin($input, $this->filename);
        return (array)Yaml::parse($content);
    }

    /**
     * If $file is provided, read its contents. Otherwise, read from input stream
     *
     * @param string $file
     * @return string
     * @throws \RuntimeException
     */
    private function readFileOrStdin(Input $input, string $file = null): string
    {
        $stream = $input->getStream() ?: STDIN;
        if ($file) {
            $contents = file_get_contents($file);
        } elseif (is_resource($stream) && 0 === ftell($stream)) {
            $contents = '';
            while (!feof($stream)) {
                $contents .= fread($stream, 1024);
            }
        } else {
            throw new \RuntimeException('Template not found in file or STDIN');
        }
        return $contents;
    }
}
