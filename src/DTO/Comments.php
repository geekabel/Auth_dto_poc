<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class Comments extends DataTransferObject {

    /** @var integer */
    public $id;
    /** @var string */
    public $title;
    /** @var string */
    public $detail;
    /** @var string */
    public $published_at;
    /** @var string */
    public $created_at;
    /** @var string */
    public $updated_at;

}