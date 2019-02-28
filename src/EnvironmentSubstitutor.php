<?php
namespace dcgen;

class EnvironmentSubstitutor
{
    /**
     * Perform replacements in $source. Strings matching {{STRING}} are replaced with corresponding STRING keys
     * in $env, or the local environment.
     * Any replacements that were not found are returned in $misses.
     *
     * @param string $source
     * @param array $env
     * @param array $misses
     * @return string
     */
    public function substitute(string $source, array $env, array &$misses): string
    {
        $missed = [];
        $output = preg_replace_callback('/{{(.+?)}}/', function ($matches) use ($env, &$missed) {
            $key = $matches[1];
            if (array_key_exists($key, $env)) {
                return $env[$key];
            }
            $value = getenv($key);
            if (!$value) {
                $missed[] = $key;
            }
            return getenv($key) ?: sprintf('{{%s}}', $key);
        }, $source);
        $misses = array_keys(array_flip($missed));
        return $output ?? '';
    }
}
