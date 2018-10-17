<?php
namespace dcgen;

class EnvironmentLoader
{
    private $filenames;

    public function __construct(array $iniFiles)
    {
        $this->filenames = $iniFiles;
    }

    public function load(): array
    {
        $settings = [];
        foreach ($this->filenames as $filename) {
            if (!file_exists($filename)) {
                throw new \RuntimeException('File not found: '.$filename);
            }
            $settings += parse_ini_file($filename);
        }
        return $settings;
    }
}
