<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankAccount
 *
 * @ORM\Table(name="bank_account")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\BankAccountRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BankAccount
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(name="title", type="string", length=255)*/
    private $title;

    /** @ORM\Column(name="account_number", type="string", length=45) */
    private $accountNumber;

    /** @ORM\Column(name="account_name", type="string", length=255) */
    private $accountName;

    /** @ORM\Column(name="branch", type="string", length=255) */
    private $branch;

    /** @ORM\Column(name="image", type="string", length=255) */
    private $image;

    /** @ORM\Column(name="sort", type="smallint") */
    private $sort;

    /** @ORM\Column(name="status", type="smallint") */
    private $status;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="CustomerPaymentBankTransfer", mappedBy="bankAccount")
     */
    private $customerPaymentBankTransfers;


    public function __construct()
    {
        $this->customerPaymentBankTransfers = new ArrayCollection();
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));
        if ($this->getCreatedAt() == null) {
          $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * Get the value of Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of Id
     *
     * @param int id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param mixed title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Account Number
     *
     * @return mixed
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set the value of Account Number
     *
     * @param mixed accountNumber
     *
     * @return self
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get the value of Account Name
     *
     * @return mixed
     */
    public function getAccountName()
    {
        return $this->accountName;
    }

    /**
     * Set the value of Account Name
     *
     * @param mixed accountName
     *
     * @return self
     */
    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;

        return $this;
    }

    /**
     * Get the value of Branch
     *
     * @return mixed
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set the value of Branch
     *
     * @param mixed branch
     *
     * @return self
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get the value of Image
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of Image
     *
     * @param mixed image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get the value of Sort
     *
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set the value of Sort
     *
     * @param mixed sort
     *
     * @return self
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get the value of Status
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of Status
     *
     * @param mixed status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of Updated At
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of Updated At
     *
     * @param mixed updatedAt
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the value of Created At
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of Created At
     *
     * @param mixed createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of Customer Payment Bank Transfers
     *
     * @return mixed
     */
    public function getCustomerPaymentBankTransfers()
    {
        return $this->customerPaymentBankTransfers;
    }

    /**
     * Set the value of Customer Payment Bank Transfers
     *
     * @param mixed customerPaymentBankTransfers
     *
     * @return self
     */
    public function setCustomerPaymentBankTransfers($customerPaymentBankTransfers)
    {
        $this->customerPaymentBankTransfers = $customerPaymentBankTransfers;

        return $this;
    }

}
