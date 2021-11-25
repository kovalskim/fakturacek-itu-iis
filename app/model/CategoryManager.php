<?php

namespace App\model;


/** Author: Dalibor Kyjovský */

use App\repository\CategoryRepository;
use Nextras\Dbal\Connection;
use Exception;

class CategoryManager
{

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var $connection */
    public $connection;

    public function __construct(CategoryRepository $categoryRepository, Connection $connection)
    {
        $this->categoryRepository = $categoryRepository;
        $this->connection = $connection;
    }


    public function delete($id)
    {
        $categories_id = $this->connection->query("SELECT `expenses`.`categories_id` FROM `expenses`")->fetchall();
        $test = 0;


        foreach ($categories_id as $used_id) {
            if ($used_id->categories_id == $id) {
                $test++;
            }

        }
        
        if($test == 0)
        {
            $this->categoryRepository->deleteCategoryByUserId($id);
        }
        else {
            return 1;
        }

    }
}