<?php

namespace App\Repository;

use Doctrine\ORM\Query;

class CustomQuery extends Query
{
    // Override methods or add custom functionality here
    public function getCustomResult()
    {
        // Custom logic to manipulate the query result
        return $this->getResult();
    }
}
