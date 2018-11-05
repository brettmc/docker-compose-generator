<?php
namespace dcgen;

class ElementRemover
{
    public function remove(array &$source, array $excluded): void
    {
        $this->internalRemove($source, $excluded);
    }

    private function internalRemove(array &$source, array $excluded, string $path = '')
    {
        if (empty($excluded)) {
            return;
        }
        foreach (array_keys($source) as $key) {
            $test = $path ? sprintf('%s.%s', $path, $key) : $key;
            if (is_string($key) && in_array($key, $excluded) === true || $this->regexContains($test, $excluded)) {
                unset($source[$key]);
            } else {
                if (is_array($source[$key])) {
                    $newPath = $path ? sprintf('%s.%s', $path, $key) : $key;
                    $this->internalRemove($source[$key], $excluded, $newPath);
                }
            }
        }
    }

    private function regexContains(string $path, array $tests): bool
    {
        foreach ($tests as $test) {
            if (preg_match(sprintf('/%s/', $test), $path) === 1) {
                return true;
            }
        }
        return false;
    }
}
