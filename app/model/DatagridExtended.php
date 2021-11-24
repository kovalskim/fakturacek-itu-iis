<?php

namespace App\model;

use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Nextras\Datagrid\Datagrid;

class DatagridExtended extends Datagrid
{
    use SecuredLinksPresenterTrait;

    /** @var callable */
    protected $banCallback;

    public function setBanCallback(callable $callback)
    {
        $this->banCallback = $callback;
    }

    public function getBanCallback(): callable
    {
        return $this->banCallback;
    }

    /**
     * @secured
     */
    public function handleBan($primary)
    {
        $call = $this->getBanCallback();
        $call($primary);
        if($this->presenter->isAjax())
        {
            $this->redrawControl('rows');
        }
    }

    /** @var callable */
    protected $allowCallback;

    public function setAllowCallback(callable $callback)
    {
        $this->allowCallback = $callback;
    }

    public function getAllowCallback(): callable
    {
        return $this->allowCallback;
    }

    /**
     * @secured
     */
    public function handleAllow($primary)
    {
        $call = $this->getAllowCallback();
        $call($primary);
        if($this->presenter->isAjax())
        {
            $this->redrawControl('rows');
        }
    }

    /** @var callable */
    protected $deleteCallback;

    public function setDeleteCallback(callable $callback)
    {
        $this->deleteCallback = $callback;
    }

    public function getDeleteCallback(): callable
    {
        return $this->deleteCallback;
    }

    /**
     * @secured
     */
    public function handleDelete($primary)
    {
        $call = $this->getDeleteCallback();
        $call($primary);
        if($this->presenter->isAjax())
        {
            $this->redrawControl('rows');
        }
    }

    /** @var callable */
    protected $resetPasswordCallback;

    public function setResetPasswordCallback(callable  $callback)
    {
        $this->resetPasswordCallback = $callback;
    }

    public function getResetPasswordCallback(): callable
    {
        return $this->resetPasswordCallback;
    }

    /**
     * @secured
     */
    public function handleResetPassword($primary)
    {
        $call = $this->getResetPasswordCallback();
        $call($primary);
    }

    /** @var callable */
    protected $changeStatusCallback;

    public function setChangeStatusCallback(callable $callback)
    {
        $this->changeStatusCallback = $callback;
    }

    public function getChangeStatusCallback(): callable
    {
        return $this->changeStatusCallback;
    }

    /**
     * @secured
     */
    public function handleChangeStatus($primary, $status)
    {
        $call = $this->getChangeStatusCallback();
        $call($primary, $status);
        if($this->presenter->isAjax())
        {
            $this->redrawControl('rows');
        }
    }

    /** @var callable */
    protected $resetEmailCallback;

    public function setResetEmailCallback(callable  $callback)
    {
        $this->resetEmailCallback = $callback;
    }

    public function getResetEmailCallback(): callable
    {
        return $this->resetEmailCallback;
    }

    /**
     * @secured
     */
    public function handleResetEmail($primary)
    {
        $call = $this->getResetEmailCallback();
        $call($primary);
    }
}