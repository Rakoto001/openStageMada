<?php

namespace App\Services;

abstract class BaseService
{
    public function save($_object){
        $this->manager->persist($_object);

        return $this->manager->flush();
    }

     /**
     * Get by id
     * @param type $_id
     * @return type
     */
    public function getById($_id)
    {
        
        return $this->getRepository()->find($_id) ;
    }

    public function removeData($_object)
    {
         $this->manager->remove($_object);

         return $this->manager->flush();

    }

    /**
     * find one object
     */
    public function findOne($_object= [])
    {

    }

    



}

  