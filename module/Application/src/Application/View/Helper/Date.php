<?php

namespace Application\View\Helper;
 
use Zend\View\Helper\AbstractHelper;

class Date extends AbstractHelper
{
    public function __invoke($date = '', $format = 'm/d/Y H:i:s')
    {
        if(!empty($date) && $date != '0000-00-00 00:00:00') {
          $dateTime = date_create($date);
          return date_format($dateTime, $format);
        }
        return '';
    }
}