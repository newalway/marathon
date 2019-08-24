<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Contact
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\ContactRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Contact
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
	   * @ORM\Column(type="string", length=255)
	   */
		private $name;

    /**
	   * @ORM\Column(name="telephone_number", type="string", length=255, nullable=true)
	   */
		private $telephoneNumber;

    /**
	   * @ORM\Column(type="string", length=255)
	   */
		private $email;

    /**
	   * @ORM\Column(type="text", length=65535, nullable=true)
	   */
		private $message;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status = 4;

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
     * Get the value of Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param mixed name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of Telephone Number
     *
     * @return mixed
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * Set the value of Telephone Number
     *
     * @param mixed telephoneNumber
     *
     * @return self
     */
    public function setTelephoneNumber($telephoneNumber)
    {
        $this->telephoneNumber = $telephoneNumber;

        return $this;
    }

    /**
     * Get the value of Email
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of Email
     *
     * @param mixed email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of Message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of Message
     *
     * @param mixed message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

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

}
