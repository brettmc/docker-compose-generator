<?php
namespace dcgen;

use Laminas\Stdlib\ArrayUtils;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Yaml\Yaml;

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
        $template = [];
        if ($input instanceof StreamableInputInterface) {
            $template = $this->yamlFromStream($input);
        }
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
     * @param StreamableInputInterface $input
     * @return array
     * @throws \RuntimeException
     */
    private function yamlFromStream(StreamableInputInterface $input): array
    {
        $stream = $input->getStream();
        if (!$stream) {
            return [];
        }
        $contents = '';
        while (!feof($stream)) {
            $contents .= fgets($stream);
        }
        return (array)Yaml::parse($contents);
    }
}
