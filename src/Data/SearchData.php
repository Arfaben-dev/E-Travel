<?php

namespace App\Data;

use App\Entity\Transport;
class SearchData
{
    /**
     * @var string
     */
     public $q = ' ';

     /**
      * @var Transport[]
      */
      public  $transport= [];

    /**
     * @var null|double
     */
      public $min;
    /**
     * @var null|double
     */
      public $max;

    /**
     * @var string
     */
      public $citystart;

    /**
     * @var string
     */
      public $cityend;

    /**
     * @var string
     */
    public $datestart;
}