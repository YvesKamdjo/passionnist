<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;


abstract class AbstractEntity implements EntityInterface
{
    /** @var int */
    protected $id;
    
    /**
     * {@inheritDoc}
     */
    public function sameIdentityAs(EntityInterface $candidate)
    {
        if ( ! $candidate instanceof self) {
            return false;
        }
        
        if ($this->getId() < 1) {
            return false;
        }
        
        return $this->getId() === $candidate->getId();
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }
}
