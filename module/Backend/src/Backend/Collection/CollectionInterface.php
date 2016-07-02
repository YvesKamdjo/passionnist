<?php

/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Collection;

use Countable;
use IteratorAggregate;
use OutOfRangeException;

interface CollectionInterface extends IteratorAggregate, Countable
{

    /**
     * Ajoute un élément à la fin de la collection
     * 
     * @param mixed $element
     * @param mixed $key
     */
    public function add($element);

    /**
     * Ajoute un élément à l'index donnée
     * 
     * @param string|int $key
     * @param mixed $element
     */
    public function set($key, $element);

    /**
     * Retourne l'élément à l'index spécifié
     * 
     * @param string $key
     * @return mixed
     * @throws OutOfRangeException
     */
    public function get($key);

    /**
     * Retourne les clés/indexes de la collection, dans l'ordre dans lequel sont
     * rangés les éléments
     *
     * @return array
     */
    public function getKeys();

    /**
     * Indique si la collection est vide ou non
     * 
     * @return boolean
     */
    public function isEmpty();

    /**
     * Indique si la collection contient l'élément passé en paramètre
     * 
     * @param mixed $candidate
     * @return boolean
     */
    public function contains($candidate);

    /**
     * Retourne une collection filtrée à partir du callable passé en paramètre
     * 
     * @param callable $filter
     * @return CollectionInterface
     */
    public function filter(callable $filter);

    /**
     * Retourne une collection qui contient tous les éléments de celle courante
     * après leurs avoir appliqué le callable
     * 
     * @param callable $map
     * @return CollectionInterface
     */
    public function map(callable $map);
}
