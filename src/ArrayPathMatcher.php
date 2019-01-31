<?php
namespace dcgen;

class ArrayPathMatcher
{
    /**
     * Does array $haystack match $needle? The arrays must match from the end
     * forwards. If $fromStart is true, they must match all the way back to
     * the start.
     *
     * @param array $haystack Path to match against
     * @param array $needle Test
     * @param bool $fromStart Does the path need to match from start-to-finish, just just at end?
     * @return bool
     */
    public static function matches(array $haystack, array $needle, bool $fromStart = false): bool
    {
        if ($fromStart) {
            //match from start
            if (count($needle) > count($haystack)) {
                return false;
            }
            for ($i=0; $i < count($haystack); $i++) {
                //$inArray = array_key_exists($i, $needle);
                //$matches = fnmatch($needle[$i], $haystack[$i]);
                if (!array_key_exists($i, $needle) || !fnmatch($needle[$i], $haystack[$i])) {
                    return false;
                }
            }
            return true;
        } else {
            //match from end
            $rev = array_reverse($haystack);
            foreach (array_reverse($needle) as $i => $val) {
                if (!fnmatch($val, $rev[$i])) {
                    return false;
                }
            }
            return true;
        }
    }
}
