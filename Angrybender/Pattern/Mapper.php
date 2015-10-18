<?php
/**
 * Created by PhpStorm.
 * User: Kir
 * Date: 07.10.2015
 * Time: 19:43
 */

namespace Angrybender\Pattern;


class Mapper implements \Iterator
{
    private $foundItems;
    private $iterator = 0;

    public function match(array $pattern, array $data)
    {
        if (!is_array($data[0])) {
            throw new \Exception("data must be an array of hashes");
        }

        $this->iterator = 0;
        $this->foundItems = [];
        foreach ($data as $item) {
            if (!is_null($isMatched = $this->matchItem($pattern, $item))) {
                $this->foundItems[] = $isMatched;
            }
        }
    }

    private function matchItem(array $pattern, array $dataHash)
    {
        $found = [];
        foreach ($pattern as $key => $typeDef) {
            if (!array_key_exists($key, $dataHash)) {
                return null;
            }

            $dataItem = $dataHash[$key];

            if (!is_null($typeDef) && gettype($typeDef) !== gettype($dataItem)) {
                return null;
            }

            if (is_scalar($typeDef) && !empty($typeDef) && ($dataItem != $typeDef)) {
                return null;
            }

            if (is_array($typeDef) && !empty($typeDef)) {
                // sub pattern like { item : { id: , value: } }
                $subFound = $this->matchItem($typeDef, $dataItem);
                if (is_null($subFound)) {
                    return null;
                }
                else {
                    $found[$key] = $subFound;
                }
            }
            else {
                $found[$key] = $dataItem;
            }
        }

        return $found;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return !empty($this->foundItems) ? $this->foundItems[$this->iterator] : null;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->iterator++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->iterator;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return array_key_exists($this->iterator, $this->foundItems);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->iterator = 0;
    }
}