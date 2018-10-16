<?php
namespace dcgen;

class ElementRemover
{
    public function remove(array &$source, array $excluded): void
    {
        if (empty($excluded)) {
            return;
        }
        foreach (array_keys($source) as $key) {
            if (is_string($key) && in_array($key, $excluded) === true) {
                unset($source[$key]);
            } else {
                if (is_array($source[$key])) {
                    $this->remove($source[$key], $excluded);
                }
            }
        }
    }
}
