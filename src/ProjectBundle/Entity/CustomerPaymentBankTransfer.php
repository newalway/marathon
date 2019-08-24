<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerPaymentBankTransfer
 *
 * @ORM\Table(name="customer_payment_bank_transfer")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\CustomerPaymentBankTransferRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CustomerPaymentBankTransfer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="CustomerOrder", inversedBy="customerPaymentBankTransfers")
     * @ORM\JoinColumn(name="customer_order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customerOrder;

    /**
    * @ORM\ManyToOne(targetEntity="BankAccount", inversedBy="customerPaymentBankTransfers")
    * @ORM\JoinColumn(name="bank_account_id", referencedColumnName="id")
    */
    private $bankAccount;

    /** @ORM\Column(name="order_number", type="string", length=45) */
    private $orderNumber;

    /** @ORM\Column(name="first_name", type="string", length=255, nullable=true) */
    private $firstName;

    /** @ORM\Column(name="last_name", type="string", length=255, nullable=true) */
    private $lastName;

    /** @ORM\Column(name="phone", type="string", length=45, nullable=true) */
    private $phone;

    /** @ORM\Column(name="amount",type="decimal", precision=10, scale=2) */
    private $amount;

    /** @ORM\Column(name="attach_file", type="string", length=255, nullable=true) */
    private $attachFile;

    /** @ORM\Column(name="date_transfer", type="date", nullable=true) */
    private $dateTransfer;

    /** @ORM\Column(name="time_transfer", type="time", nullable=true) */
    private $timeTransfer;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

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
     * Get id
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
     * Get the value of Order Number
     *
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set the value of Order Number
     *
     * @param mixed orderNumber
     *
     * @return self
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get the value of Bank Account
     *
     * @return mixed
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * Set the value of Bank Account
     *
     * @param mixed bankAccount
     *
     * @return self
     */
    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * Get the value of First Name
     *
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of First Name
     *
     * @param mixed firstName
     *
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of Last Name
     *
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of Last Name
     *
     * @param mixed lastName
     *
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of Phone
     *
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of Phone
     *
     * @param mixed phone
     *
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of Amount
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of Amount
     *
     * @param mixed amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of Attach File
     *
     * @return mixed
     */
    public function getAttachFile()
    {
        return $this->attachFile;
    }

    /**
     * Set the value of Attach File
     *
     * @param mixed attachFile
     *
     * @return self
     */
    public function setAttachFile($attachFile)
    {
        $this->attachFile = $attachFile;

        return $this;
    }

    /**
     * Get the value of Date Transfer
     *
     * @return mixed
     */
    public function getDateTransfer()
    {
        return $this->dateTransfer;
    }

    /**
     * Set the value of Date Transfer
     *
     * @param mixed dateTransfer
     *
     * @return self
     */
    public function setDateTransfer($dateTransfer)
    {
        $this->dateTransfer = $dateTransfer;

        return $this;
    }

    /**
     * Get the value of Time Transfer
     *
     * @return mixed
     */
    public function getTimeTransfer()
    {
        return $this->timeTransfer;
    }

    /**
     * Set the value of Time Transfer
     *
     * @param mixed timeTransfer
     *
     * @return self
     */
    public function setTimeTransfer($timeTransfer)
    {
        $this->timeTransfer = $timeTransfer;

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
     * Get the value of Customer Order
     *
     * @return mixed
     */
    public function getCustomerOrder()
    {
        return $this->customerOrder;
    }

    /**
     * Set the value of Customer Order
     *
     * @param mixed customerOrder
     *
     * @return self
     */
    public function setCustomerOrder($customerOrder)
    {
        $this->customerOrder = $customerOrder;

        return $this;
    }

}
