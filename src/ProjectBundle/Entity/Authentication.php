<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Authentication
 *
 * @ORM\Table(name="authentication",
 *          indexes={@ORM\Index(name="authentication_l_1", columns={"name"}),
 *                   @ORM\Index(name="authentication_l_2", columns={"group_type"})})
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\AuthenticationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Authentication
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
     * @ORM\Column(type="text", length=65535, nullable=true)
    */
    private $value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */
    private $title;

    /**
     * @ORM\Column(name="input_type", type="string", length=255, nullable=true)
    */
    private $inputType;

    /**
     * @ORM\Column(name="group_type", type="string", length=255, nullable=true)
    */
    private $groupType;


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
     * Get the value of Value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of Value
     *
     * @param mixed value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

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
     * Get the value of Input Type
     *
     * @return mixed
     */
    public function getInputType()
    {
        return $this->inputType;
    }

    /**
     * Set the value of Input Type
     *
     * @param mixed inputType
     *
     * @return self
     */
    public function setInputType($inputType)
    {
        $this->inputType = $inputType;

        return $this;
    }

    /**
     * Get the value of Group Type
     *
     * @return mixed
     */
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * Set the value of Group Type
     *
     * @param mixed groupType
     *
     * @return self
     */
    public function setGroupType($groupType)
    {
        $this->groupType = $groupType;

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
