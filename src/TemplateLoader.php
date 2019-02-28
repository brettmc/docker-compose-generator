<?php
namespace dcgen;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;
use Zend\Stdlib\ArrayUtils;

class TemplateLoader
{
    /**
     * Load and merge one or more YAML templates from stream input and/or files.
     * Stream input takes precedence if there are both.
     *
     * @param InputInterface $input
     * @param array $filenames
     * @return array
     * @throws \InvalidArgumentException
     */
    public function load(InputInterface $input, array $filenames): array
    {
        $template = $this->yamlFromStream($input);
        if (empty($template) && empty($filenames)) {
            throw new \InvalidArgumentException('No input provided');
        }
        foreach ($filenames as $filename) {
            if (!file_exists($filename)) {
                throw new \InvalidArgumentException('File does not exist: '.$filename);
            }
            $content = (array)Yaml::parse(file_get_contents($filename));
            $template = ArrayUtils::merge($template, $content);
        }
        return $template;
    }

    /**
     * Read and parse YAML document from input stream
     *
     * @param InputInterface $input
     * @return array
     * @throws \RuntimeException
     */
    private function yamlFromStream(InputInterface $input): array
    {
        $stream = $input->getStream() ?: STDIN;
        $arr = [];
        if (is_resource($stream) && 0 === ftell($stream)) {
            $contents = '';
            while (!feof($stream)) {
                $contents .= fread($stream, 1024);
            }
            $arr = (array)Yaml::parse($contents);
        }
        return $arr;
    }
}
