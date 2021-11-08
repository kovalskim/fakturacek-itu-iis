<?php

namespace App\PublicModule\model;

use Exception;
use Nextras\Dbal\Connection;

class UserManager
{
    /** @var $connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function registrationFormSucceeded($form, $values)
    {
        //$values->email;
        throw new Exception("nwenwqn");

    }

    public function registrationFormValidate($form, $values)
    {

    }


}
