<?php
/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Slugify extends AbstractHelper
{
    /**
     * Transforme une chaine passée en paramètre en slug
     * 
     * @param string $string
     * @return string
     */
    public function __invoke($string)
    {
        $string = preg_replace('/[^\\p{L}\\d]+/u', '-', $string);
        $string = trim($string, '-');
        $string = iconv('utf-8', 'ASCII//TRANSLIT', $string);
        $string = strtolower($string);
        $string = preg_replace('/[^-\w]+/', '', $string);
        
        return $string;
    }
}
