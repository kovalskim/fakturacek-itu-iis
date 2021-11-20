<?php

namespace App\PublicModule\model;

/** Author: Martin Kovalski */

use Nette\Security\User;
use Nette\Utils\Paginator;
use Nextras\Datagrid\Datagrid;
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

    public function __construct(Connection $connection, User $user)
    {
        $this->connection = $connection;
        $this->user = $user;
    }

    public function createDatagrid($table, $presenter): Datagrid
    {
        /** Name of table in database as global property */
        $this->table = $table;

        /** Get presenter name and module for path to template */
        $this->presenter_params = explode(':', $presenter, 2);

        $grid = new Datagrid();

        /** Primary key is always id */
        $grid->setRowPrimaryKey('id');

        $grid->setDatasourceCallback([$this, 'getDataSource']);

        /** Pagination */
        $grid->setPagination(10, function ($filter, $order) {
            return $this->getDataSum($filter, $order, $this->table);
        });

        $grid->addCellsTemplate(__DIR__ . '/../../' . $this->presenter_params[0] . 'Module/templates/' . $this->presenter_params[1] . '/@cells.latte');

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
        if($this->presenter_params[1] == 'Clients')
        {
            $user_id = $this->user->getId();
            $builder->andWhere('users_id = %i', $user_id);
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