<?php

namespace App\model;

/** Author: Martin Kovalski, Radek Jůzl, Dalibor Kyjovský */

use Nette\Security\User;
use Nette\Utils\Paginator;
use Nextras\Dbal\Connection;
use Nextras\Dbal\Result\Result;

class DatagridManager
{
    /** @var Connection */
    private $connection;

    /** @var User */
    public $user;

    private $table;
    private $presenter_params;
    private $client_id;
    private $type;

    public function __construct(Connection $connection, User $user)
    {
        $this->connection = $connection;
        $this->user = $user;
    }

    public function createDatagrid($table, $presenter, $client_id = null, $type = null): DatagridExtended
    {
        /** Name of table in database as global property */
        $this->table = $table;
        $this->client_id = $client_id;
        $this->type = $type;

        /** Get presenter name and module for path to template */
        $this->presenter_params = explode(':', $presenter, 2);

        $grid = new DatagridExtended();

        /** Primary key is always id */
        $grid->setRowPrimaryKey('id');

        $grid->setDatasourceCallback([$this, 'getDataSource']);

        /** Pagination */
        $grid->setPagination(10, function ($filter, $order) {
            return $this->getDataSum($filter, $order, $this->table);
        });

        if($client_id)
        {
            if($type == "invoices")
            {
                $grid->addCellsTemplate(__DIR__ . '/../' . $this->presenter_params[0] . 'Module/templates/' . $this->presenter_params[1] . '/@cellsInvoices.latte');
            }
            else
            {
                $grid->addCellsTemplate(__DIR__ . '/../' . $this->presenter_params[0] . 'Module/templates/' . $this->presenter_params[1] . '/@cellsExpenses.latte');
            }
        }
        else
        {
            $grid->addCellsTemplate(__DIR__ . '/../' . $this->presenter_params[0] . 'Module/templates/' . $this->presenter_params[1] . '/@cells.latte');
        }

        return $grid;
    }

    public function getDataSum($filter, $order): int
    {
        return $this->getDataSource($filter, $order)->count();
    }

    public function getDataSource($filter, $order, Paginator $paginator = NULL): Result
    {
        $builder = $this->connection->createQueryBuilder();

        $builder->from($this->table);

        if($this->presenter_params[1] == 'Administrators')
        {
            $builder->andWhere('role = %s', 'admin');
        }
        elseif($this->presenter_params[1] == 'Users')
        {
            $builder->select('*, users.id as id, users_last_password_change.timestamp as password_timestamp, users_last_login.timestamp as login_timestamp');
            $builder->joinLeft('users_last_password_change', 'users.id = users_last_password_change.users_id');
            $builder->joinLeft('users_last_login', 'users.id = users_last_login.users_id');
           $builder->andWhere("role != %s", "admin");
        }
        elseif($this->presenter_params[1] == 'Expenses')
        {   //TODO: nejde ajax a upravy s tim
           // $builder->select('*, expenses.categories_id as id_category, categories.name as category_name');
           // $builder->joinLeft('categories', 'expenses.categories_id = categories.id');
           // $builder->andWhere("role != %s", "admin");
        }
        elseif($this->presenter_params[1] == 'Invoicing')
        {
            $user_id = $this->user->getId();
            $builder->andWhere('users_id = %i', $user_id);
        }
        if($this->presenter_params[0] == 'Business')
        {
            if($this->presenter_params[1] == 'Clients')
            {
                $user_id = $this->user->getId();
                $builder->andWhere('users_id = %i', $user_id);
            }
            //Expenses only from actual user
            elseif($this->presenter_params[1] == 'Expenses')
            {
                $user_id = $this->user->getId();
                $builder->andWhere('users_id = %i', $user_id);
            }
            //Cant view default category
            elseif($this->presenter_params[1] == 'Category')
            {
                $user_id = $this->user->getId();
                $builder->andWhere('users_id = %i', $user_id);
            }
        }
        else
        {
            if($this->presenter_params[1] == 'Clients')
            {
                if($this->table == "accountant_permission")
                {
                    $accountant_id = $this->user->getId();
                    $builder->joinLeft('users', 'accountant_permission.users_id = users.id');
                    $builder->andWhere('accountant_permission.accountant_id = %i', $accountant_id);
                }
                elseif($this->table == "invoices")
                {
                    $builder->andWhere('users_id = %i', $this->client_id);
                }
                elseif($this->table == "expenses")
                {
                    $builder->joinLeft("categories", "expenses.categories_id = categories.id");
                    $builder->andWhere('users_id = %i', $this->client_id);
                }
            }
        }

        /** Filter - where */
        foreach ($filter as $k => $v) {
            if (is_array($v))
            {
                $builder->andWhere('%column IN %s[]', $k, $v);
            }
            else
            {
                $builder->andWhere('%column LIKE %s', $k, "%$v%");
            }
        }

        /** Pagination - limit */
        if ($paginator) {
            $builder->limitBy($paginator->getItemsPerPage(), $paginator->getOffset());
        }

        /** Order*/
        if (isset($order[0])) {
            $builder->orderBy(implode(' ', $order));
        }

        return $this->connection->queryByQueryBuilder($builder);
    }
}