<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class Role extends DataTransferObject {

    /** @var integer */
    public $id;
    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /** @var string */
    public $type;
}