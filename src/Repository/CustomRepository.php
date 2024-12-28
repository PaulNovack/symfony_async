<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class CustomRepository extends EntityRepository
{
    // Add your custom methods here
    public function findAsync($id)
    {
        // Custom logic for asynchronous fetching
        return $this->find($id);
    }
}
