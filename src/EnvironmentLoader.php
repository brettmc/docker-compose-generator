<?php
namespace dcgen;

class EnvironmentLoader
{
    private $settings = [];

    public function load(array $iniFiles): void
    {
        foreach ($iniFiles as $filename) {
            if (!file_exists($filename)) {
                throw new \RuntimeException('File not found: '.$filename);
            }
            $this->settings = array_merge($this->settings, parse_ini_file($filename));
        }
    }

    public function get(): array
    {
        return $this->settings;
    }

    public function add(array $settings): void
    {
        $this->settings = array_merge($this->settings, $settings);
    }
}
