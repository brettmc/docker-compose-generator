<?php
namespace dcgen;

class EnvironmentLoader
{
    /**
     * @var array
     */
    private $settings = [];

    /**
     * @param array $iniFiles
     * @return void
     * @throws \RuntimeException
     */
    public function load(array $iniFiles): void
    {
        foreach ($iniFiles as $filename) {
            if (!file_exists($filename)) {
                throw new \RuntimeException('File not found: '.$filename);
            }
            $this->settings = array_merge($this->settings, parse_ini_file($filename, false, INI_SCANNER_RAW));
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
