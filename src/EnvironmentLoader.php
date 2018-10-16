<?php
namespace dcgen;

class EnvironmentLoader
{
    private $filename;

    public function __construct(string $iniFile)
    {
        $this->filename = $iniFile;
        if (!file_exists($this->filename)) {
            throw new \InvalidArgumentException('File not found: '.$this->filename);
        }
    }

    public function load(): array
    {
        return parse_ini_file($this->filename);
    }
}
