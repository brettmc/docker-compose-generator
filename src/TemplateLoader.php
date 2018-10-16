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
     * If $file is provided, read its contents. Otherwise, read from STDIN
     *
     * @param string $file
     * @return string
     * @throws \RuntimeException
     */
    private function readFileOrStdin(Input $input, string $file = null): string
    {
        if ($file) {
            $contents = file_get_contents($file);
        //} else if (is_resource($input->getStream()) && 0 === ftell($input->getStream())) {
        } elseif (0 === ftell(STDIN)) {
            $contents = '';
            while (!feof(STDIN)) {
                $contents .= fread(STDIN, 1024);
            }
        } else {
            throw new \RuntimeException('Template not found in file or STDIN');
        }
        return $contents;
    }
}
