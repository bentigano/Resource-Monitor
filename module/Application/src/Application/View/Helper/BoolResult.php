<?php

namespace Application\View\Helper;
 
use Zend\View\Helper\AbstractHelper;

class BoolResult extends AbstractHelper
{
    public function __invoke($tinyInt)
    {
        if (is_null($tinyInt))
        {
            return '<span class="label">Not Yet Tested</span>';
        }
        if ($tinyInt == 1)
        {
            return '<span class="label label-success">Success</span>';
        }
        return '<span class="label label-important">Failure</span>';
    }
}