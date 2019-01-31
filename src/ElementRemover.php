<?php
namespace dcgen;

class ElementRemover
{
    public function remove(array &$source, array $excluded, string $fs = '.'): void
    {
        if (empty($excluded)) {
            return;
        }
        $new = [];
        //split excluded
        foreach ($excluded as $string) {
            $key = $string;
            $fromStart = false;
            if (substr($string, 0, 1) === '^') {
                $string = substr($string, 1);
                $fromStart = true;
            }
            $new[$key] = [
                'path' => explode($fs, $string),
                'fromStart' => $fromStart,
            ];
        }
        $this->internalRemove($source, $new);
    }

    private function internalRemove(array &$source, array $excluded, array $path = [])
    {
        $reindex = false;
        foreach ($source as $key => $value) {
            $newPath = array_merge($path, [$key]);
            if ($this->matches($newPath, $excluded)) {
                unset($source[$key]);
                continue;
            }
            if (!is_array($source[$key])) {
                $valuePath = is_int($key) ? array_merge($path, [$value]) : array_merge($newPath, [$value]);
                if ($this->matches($valuePath, $excluded)) {
                    unset($source[$key]);
                    is_int($key) && $reindex = $reindex || is_int($key);
                }
                continue;
            }
            if (is_array($source[$key])) {
                $this->internalRemove($source[$key], $excluded, $newPath);
            }
        }
        if ($reindex) {
            $source = array_values($source);
        }
    }

    private function matches(array $path, array $excluded): bool
    {
        foreach ($excluded as $exclude) {
            if (ArrayPathMatcher::matches($path, $exclude['path'], $exclude['fromStart'])) {
                return true;
            }
        }
        return false;
    }
}
