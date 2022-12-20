<?php

namespace App\DataDefinitions\Providers;

use IteratorIterator;
use Traversable;
use Wvision\Bundle\DataDefinitionsBundle\Provider\ImportDataSetInterface;

class ChunkedIteratorDataset implements ImportDataSetInterface
{
    private IteratorIterator $iterator;
    private int $offset;
    private int $limit;
    private int $at;

    public function __construct(Traversable $iterator, int $offset = 0, int $limit = null)
    {
        $this->iterator = new IteratorIterator($iterator);
        $this->offset = $offset;
        $this->at = 0;
        $this->limit = $offset + $limit;
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current(): mixed
    {
        while ($this->offset && $this->at < $this->offset) {
            $this->iterator->next();
            $this->at++;
        }
        return $this->iterator->current();
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        while ($this->offset && $this->at < $this->offset) {
            $this->iterator->next();
            $this->at++;
        }
        $this->iterator->next();
        $this->at++;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): mixed
    {
        return $this->iterator->key();
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        return $this->at < $this->limit && $this->iterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        $this->iterator->rewind();
    }
}
