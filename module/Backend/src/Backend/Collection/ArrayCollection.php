<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Collection;

use ArrayIterator;
use Backend\Domain\Common\Entity\EntityInterface;
use InvalidArgumentException;
use OutOfRangeException;
use Traversable;

class ArrayCollection implements CollectionInterface
{
    /** @var array */
    private $elements;
    
    /**
     * @param array $elements
     * @return ArrayCollection
     */
    public static function createFromArray(array $elements)
    {
        $collection = new static();
        
        array_walk($elements, function($value, $key) use ($collection) {
            $collection->set($key, $value);
        });
        
        return $collection;
    }
    
    /**
     * @param array $elements
     * @return ArrayCollection
     */
    public static function merge(
        ArrayCollection $arrayCollection1,
        ArrayCollection $arrayCollection2
    ) {
        $collection = new static();
        
        $elements = array_merge(
            $arrayCollection1->elements,
            $arrayCollection2->elements
        );
        
        return $collection->createFromArray($elements);
    }
    
    /**
     * 
     */
    public function __construct()
    {
        $this->elements = array();
    }
    
    /**
     * Interface to create an external Iterator.
     * 
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->elements);
    }
    
    /**
     * @param mixed $element
     */
    public function add($element)
    {
        $this->elements[] = $element;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $element)
    {
        if (is_string($key) === false && is_int($key) === false) {
            throw new InvalidArgumentException(
                "L'index de stockage de la collection doit être un entier ou une chaine de caractères"
            );
        }
        
        $this->elements[$key] = $element;
    }
    
    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->elements) === false) {
            throw new OutOfRangeException(
                "L'index de stockage demandé n'existe pas"
            );
        }
        
        return $this->elements[$key];
    }
    
    /**
     * {@inheritDoc}
     */
    public function getKeys()
    {
        return array_keys($this->elements);
    }
    
    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->elements);
    }
    
    /**
     * {@inheritDoc}
     */
    public function isEmpty()
    {
        return empty($this->elements);
    }
    
    /**
     * {@inheritDoc}
     */
    public function contains($candidate)
    {
        // Recherche une entité dans la collection
        if ($candidate instanceof EntityInterface) {
            foreach ($this->elements as $element) {
                if ($candidate->sameIdentityAs($element) === true) {
                    return true;
                }
            }
            return false;
        }
        
        // Recherche un scalaire dans la collection
        return in_array($candidate, $this->elements, true);
    }
    
    /**
     * {@inheritDoc}
     */
    public function filter(callable $filter)
    {
        return self::createFromArray(array_filter($this->elements, $filter));
    }
    
    /**
     * {@inheritDoc}
     */
    public function map(callable $map)
    {
        return self::createFromArray(array_map($map, $this->elements));
    }
}