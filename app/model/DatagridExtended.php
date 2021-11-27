<?php

/** Author: Martin Kovalski, Radek Jůzl, Dalibor Kyjovský*/

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
        if($this->presenter->isAjax())
        {
            $this->redrawControl('rows');
        }
    }

    /** @var callable */
    protected $downloadInvoiceCallback;

    public function setDownloadInvoiceCallback(callable $callback)
    {
        $this->downloadInvoiceCallback = $callback;
    }

    public function getDownloadInvoiceCallback(): callable
    {
        return $this->downloadInvoiceCallback;
    }

    /**
     * @secured
     */
    public function handleDownloadInvoice($primary)
    {
        $call = $this->getDownloadInvoiceCallback();
        $call($primary);
    }


    /** @var callable */
    protected $editCategoryCallback;

    public function setEditCategoryCallback(callable  $callback)
    {
        $this->editCategoryCallback = $callback;
    }

    public function getEditCategoryCallback(): callable
    {
        return $this->editCategoryCallback;
    }

    /**
     * @secured
     */
    public function handleEditCategory($primary)
    {
        $call = $this->getEditCategoryCallback();
        $call($primary);
        if($this->presenter->isAjax())
        {
            $this->redrawControl('rows');
        }
    }



    //** Deleting category */


    /** @var callable */
    protected $deleteCategoryCallback;

    public function setDeleteCategoryCallback(callable $callback)
    {
        $this->deleteCategoryCallback = $callback;
    }

    public function getDeleteCategoryCallback(): callable
    {
        return $this->deleteCategoryCallback;
    }

    /**
     * @secured
     */
    public function handleDeleteCategory($primary)
    {
        $call = $this->getDeleteCategoryCallback();
        $call($primary);
        if($this->presenter->isAjax())
        {
            $this->redrawControl('rows');
        }
    }


    //** Deleting expense */


    /** @var callable */
    protected $deleteExpenseCallback;

    public function setDeleteExpenseCallback(callable $callback)
    {
        $this->deleteExpenseCallback = $callback;
    }

    public function getDeleteExpenseCallback(): callable
    {
        return $this->deleteExpenseCallback;
    }

    /**
     * @secured
     */
    public function handleDeleteExpense($primary)
    {
        $call = $this->getDeleteExpenseCallback();
        $call($primary);
        if($this->presenter->isAjax())
        {
            $this->redrawControl('rows');
        }
    }

    /** @var callable */
    protected $sendReminderCallback;

    public function setSendReminderCallback(callable $callback)
    {
        $this->sendReminderCallback = $callback;
    }

    public function getSendReminderCallback(): callable
    {
        return $this->sendReminderCallback;
    }

    /**
     * @secured
     */
    public function handleSendReminder($primary)
    {
        $call = $this->getSendReminderCallback();
        $call($primary);
    }

}
