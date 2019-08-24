<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SettingOption
 *
 * @ORM\Table(name="setting_option",
 *          indexes={ @ORM\Index(name="setting_option_l_1", columns={"cat_type"}),
 *                      @ORM\Index(name="setting_option_option_name_1", columns={"option_name"}) })
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\SettingOptionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SettingOption
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
     * @ORM\Column(name="option_name", type="string", length=255)
    */
    private $optionName;

    /**
     * @ORM\Column(name="option_value", type="text", nullable=true)
    */
    private $optionValue;

    /**
     * @ORM\Column(name="option_title", type="string", length=255, nullable=true)
    */
    private $optionTitle;

    /**
     * @ORM\Column(name="option_type", type="string", length=255, nullable=true)
    */
    private $optionType;

    /**
     * @ORM\Column(name="group_type", type="string", length=255, nullable=true)
    */
    private $groupType;

    /**
     * @ORM\Column(name="cat_type", type="string", length=255, nullable=true)
    */
    private $catType;

    /**
	* @ORM\Column(name="param", type="array", nullable = true)
	*/
	private $param;

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
     * Get the value of Option Name
     *
     * @return mixed
     */
    public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * Set the value of Option Name
     *
     * @param mixed optionName
     *
     * @return self
     */
    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;

        return $this;
    }

    /**
     * Get the value of Option Value
     *
     * @return mixed
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Set the value of Option Value
     *
     * @param mixed optionValue
     *
     * @return self
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;

        return $this;
    }

    /**
     * Get the value of Option Title
     *
     * @return mixed
     */
    public function getOptionTitle()
    {
        return $this->optionTitle;
    }

    /**
     * Set the value of Option Title
     *
     * @param mixed optionTitle
     *
     * @return self
     */
    public function setOptionTitle($optionTitle)
    {
        $this->optionTitle = $optionTitle;

        return $this;
    }

    /**
     * Get the value of Option Type
     *
     * @return mixed
     */
    public function getOptionType()
    {
        return $this->optionType;
    }

    /**
     * Set the value of Option Type
     *
     * @param mixed optionType
     *
     * @return self
     */
    public function setOptionType($optionType)
    {
        $this->optionType = $optionType;

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
     * Get the value of Cat Type
     *
     * @return mixed
     */
    public function getCatType()
    {
        return $this->catType;
    }

    /**
     * Set the value of Cat Type
     *
     * @param mixed catType
     *
     * @return self
     */
    public function setCatType($catType)
    {
        $this->catType = $catType;

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
     * Get the value of Param
     *
     * @return mixed
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set the value of Param
     *
     * @param mixed param
     *
     * @return self
     */
    public function setParam($param)
    {
        $this->param = $param;

        foreach ($param as $pa) {
            $this->addParam($pa);
        }

        return $this;
    }

    public function addParam($pa)
    {
        if (!in_array($pa, $this->param, true)) {
            $this->param[] = $pa;
        }

        return $this;
    }

}
