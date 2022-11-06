<?php

namespace App\DTO;
use Spatie\DataTransferObject\DataTransferObject;

class ApiUser extends DataTransferObject {

    /** @var integer */
    public $id;
    /** @var string */
    public $username;
    /** @var string */
    public $email;
    /** @var string */
    public $provider;
    /** @var bool */
    public $confirmed;
    /** @var \App\DTO\Role */
    public $role;
    /** @var string */
    public $phone;
    /** @var string */
    public $created_at;
    /** @var string */
    public $updated_at;
    /** @var \App\DTO\Microjobs[] */
    public $microjobs;
    /** @var \App\DTO\Comments */
    public $comments;
}