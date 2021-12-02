<?php

/** Author: Martin Kovalski, Radek Jůzl, Dalibor Kyjovský */

namespace App\model;

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

    public function __construct(Connection $connection, User $user)
    {
        $this->connection = $connection;
        $this->user = $user;
    }

    /**
     * Template for creating datagrid
     */
    public function createDatagrid($table, $presenter, $client_id = null, $type = null): DatagridExtended
    {
        /** Name of table in database as global property */
        $this->table = $table;
        $this->client_id = $client_id;

        /** Get presenter name and module for path to template */
        $this->presenter_params = explode(':', $presenter, 2);

        $grid = new DatagridExtended();

        /** Primary key is always id */
        if($this->presenter_params[1] == "Category")
        {
            $grid->setRowPrimaryKey('cat_id');
        }
        else
        {
            $grid->setRowPrimaryKey('id');
        }

        $grid->setDatasourceCallback([$this, 'getDataSource']);

        /** Pagination */
        $grid->setPagination(10, function ($filter, $order) {
            return $this->getDataSum($filter, $order);
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

        $grid->addCellsTemplate(__DIR__ . '/../components/@bootstrap3.datagrid.latte');

        return $grid;
    }

    /**
     * Count data in datagrid for pagination
     */
    public function getDataSum($filter, $order): int
    {
        return $this->getDataSource($filter, $order)->count();
    }

    /**
     * Prepare data for datagrid
     */
    public function getDataSource($filter, $order, Paginator $paginator = NULL): Result
    {
        $builder = $this->connection->createQueryBuilder();

        $builder->from($this->table);

        if($this->presenter_params[1] == 'Administrators')
        {
            $builder->andWhere('role = %s', 'admin');
        }
        elseif($this->presenter_params[1] == 'Users') /** Joins between tables */
        {
            $builder->select('*, users.id as id, users_last_password_change.timestamp as password_timestamp, users_last_login.timestamp as login_timestamp');
            $builder->joinLeft('users_last_password_change', 'users.id = users_last_password_change.users_id');
            $builder->joinLeft('users_last_login', 'users.id = users_last_login.users_id');
           $builder->andWhere("role != %s", "admin");
        }
        elseif($this->presenter_params[1] == 'Expenses')
        {
            $user_id = $this->user->getId();
            $builder->select("*, expenses.id as id, expenses.users_id as users_id, categories.users_id as cat_users_id");
            $builder->joinLeft("categories", "expenses.expenses_cat_id = categories.cat_id");
            $builder->andWhere('expenses.users_id = %i', $user_id);
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
                    $builder->select("*, expenses.id as id, categories.cat_id as cat_id");
                    $builder->joinLeft("categories", "expenses.expenses_cat_id = categories.cat_id");
                    $builder->andWhere('expenses.users_id = %i', $this->client_id);
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