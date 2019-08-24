<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * TemplateCustomerGroup
 *
 * @ORM\Table(name="template_customer_group")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\TemplateCustomerGroupRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TemplateCustomerGroup
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="CustomerGroup", inversedBy="templateCustomerGroups")
     * @ORM\JoinColumn(name="customer_group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customerGroup;

    /**
     * Many TemplateCustomerGroup have Many ProductCategory.
     * @ORM\ManyToMany(targetEntity="ProductCategory", mappedBy="templateCustomerGroups")
     */
    private $productCategorys;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position = 0;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;


    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public function __construct()
    {
        $this->productCategorys = new ArrayCollection();
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
     * Get the value of Customer Group
     *
     * @return mixed
     */
    public function getCustomerGroup()
    {
        return $this->customerGroup;
    }

    /**
     * Set the value of Customer Group
     *
     * @param mixed customerGroup
     *
     * @return self
     */
    public function setCustomerGroup($customerGroup)
    {
        $this->customerGroup = $customerGroup;

        return $this;
    }

    /**
     * Get the value of Many TemplateCustomerGroup have Many ProductCategory.
     *
     * @return mixed
     */
    public function getProductCategorys()
    {
        return $this->productCategorys;
    }

    /**
     * Set the value of Many TemplateCustomerGroup have Many ProductCategory.
     *
     * @param mixed productCategorys
     *
     * @return self
     */
    public function setProductCategorys($productCategorys)
    {
        $this->productCategorys = $productCategorys;

        return $this;
    }

    /**
     * @param mixed productCategorys
     */
    public function removeProductCategorys(ProductCategory $productCategory)
    {
        if (false === $this->productCategorys->contains($productCategory)) {
            return;
        }
        $this->productCategorys->removeElement($productCategory);
        $productCategory->removeTemplateCustomerGroups($this);
    }

    /**
     * @param mixed productCategorys
     */
    public function addProductCategorys(ProductCategory $productCategory)
    {
        if (true === $this->productCategorys->contains($productCategory)) {
            return;
        }
        $this->productCategorys->add($productCategory);
        $productCategory->addTemplateCustomerGroups($this);
    }


    /**
     * Get the value of Position
     *
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of Position
     *
     * @param mixed position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

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
