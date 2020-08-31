<?php
declare(strict_types=1);

namespace App\Libs\Kit;

/**
 * Kit for processing array
 */
class ArrayKit
{
    const FILTER_FUZZY  = 'fuzzy';
    const FILTER_VALUE  = 'value';
    const FILTER_STRICT = 'strict';
    const FILTER_TYPE   = 'type';

    /**
     * set array content
     *     set($array, 'key', $content)                      => $array['key'] = $content;
     *     set($array, ['level_1', 'level_2'], $content)     => $array['level_1']['level_2'] = $content;
     *     set($array, ['level_1', 'level_2', ''], $content) => $array['level_1']['level_2'][] = $content;
     * @param  string/array $key     array key
     * @param  mixed        $content array content
     * @return mixed
     */
    public static function set(array &$array, $key, $content)
    {
        if (is_array($key)) {
            $target = &$array;
            foreach ($key as $keySegment) {
                if ($keySegment === '') {
                    $target[] = array();
                    end($target);
                    $lastKey = key($target);
                    $target = &$target[$lastKey];
                } else {
                    if (!isset($target[$keySegment])) {
                        $target[$keySegment] = array();
                    }
                    $target = &$target[$keySegment];
                }
            }
            return $target = $content;
        } else {
            return $array[$key] = $content;
        }
    }

    /**
     * get array content
     *     get($array, 'key')                  => return $array['key'];
     *     get($array, ['level_1', 'level_2']) => retunr $array['level_1']['level_2'];
     * @param  string/array $key array key
     * @return mixed
     */
    public static function get(array $array, $key)
    {
        if (is_array($key)) {
            $target = $array;
            foreach ($key as $keySegment) {
                if (array_key_exists($keySegment, $target)) {
                    $target = $target[$keySegment];
                } else {
                    return null;
                }
            }
            return $target;
        } else {
            return array_key_exists($key, $array) ? $array[$key] : null;
        }
    }
}
