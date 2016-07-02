<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Backend\Entity;

class BookingComment
{
    private $idBooking;
    private $rate;
    private $comment;

    public function getIdBooking()
    {
        return $this->idBooking;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setIdBooking($idBooking)
    {
        $this->idBooking = $idBooking;
        return $this;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

}
