<?php

namespace Application\View\Helper;
 
use Zend\View\Helper\AbstractHelper;

class Time extends AbstractHelper
{
    public function __invoke($dateTime = '', $format = 'H:i')
    {
        if(!empty($dateTime) && $dateTime != '0000-00-00 00:00:00') {
          $dateTime = date_create($dateTime);
          return date_format($dateTime, $format);
        }
        return '';
    }
}