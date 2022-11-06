<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class Microjobs extends DataTransferObject {

    /** @var integer */
    public $id;
    /** @var string */
    public $title;
    /** @var string */
    public $description;
    /** @var integer */
    public $price;
    /** @var string */
    public $location;
    /** @var string */
    public $published_at;
    /** @var string */
    public $created_at;
    /** @var string */
    public $updated_at;
    /** @var string */
    public $image;
}