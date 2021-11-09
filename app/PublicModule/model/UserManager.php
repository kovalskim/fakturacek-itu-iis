<?php

namespace App\PublicModule\model;

use App\PublicModule\repository\UserRepository;
use Exception;
use Nextras\Dbal\Connection;

class UserManager
{
    /** @var $connection */
    private $connection;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(Connection $connection, UserRepository $userRepository)
    {
        $this->connection = $connection;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws Exception
     */
    public function registrationFormSucceeded($form, $values)
    {
        throw new Exception("Registrace nebyla úspěšná");
    }

    public function registrationFormValidate($form, $values)
    {
        $email = $values->email;
        $cin = $values->cin;

        if(($this->userRepository->getUserByEmail($email)) != null)
        {
            //Todo: co tu udelat? Throw?
        }
        if(($this->userRepository->getUserByCin($cin)) != null)
        {

        }
    }

    public function registrationFormInsert($form, $values)
    {
        $cin = $values->cin;
        $name = $values->name;
        $email = $values->email;
        $phone = $values->phone;
        $password = $values->password;
        $role = $values->role;
        $street = $values->street;
        $city = $values->city;
        $zip = $values->zip;

        if($role == 0)
        {
            $role = "business";
        }
        else
        {
            $role = "accountant";
        }
        //TODO: Heslo takhle? a co ten HASH v tabulce?
        $passwords = new Passwords(PASSWORD_BCRYPT, ['cost' => 12]);
        $password_hash = $passwords->hash($password);

        if(($this->userRepository->insertUser($cin, $name, $email, $phone, $password_hash, $role, $street, $city, $zip)) != true)
        {

        }
    }


}
