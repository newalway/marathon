<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * CustomerGroup
 *
 * @ORM\Table(name="customer_group")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\CustomerGroupRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CustomerGroup
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
	* @ORM\Column(name="icon", type="string", length=255, nullable = true)
	*/
	private $icon;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position = 0;

    /** @ORM\Column(name="updated_at", type="datetime", nullable = true) */
    private $updatedAt;

    /** @ORM\Column(name="created_at", type="datetime") */
    private $createdAt;

    /**
     * Many CustomerGroup have Many Products.
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="customerGroups")
     */
    private $products;

    /**
	* @ORM\OneToMany(targetEntity="TemplateCustomerGroup", mappedBy="customerGroup")
	*/
	private $templateCustomerGroups;


    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->templateCustomerGroups = new ArrayCollection();
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

    /**
     * Get the value of Many CustomerGroup have Many Products.
     *
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set the value of Many CustomerGroup have Many Products.
     *
     * @param mixed products
     *
     * @return self
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @param mixed products
     */
    public function removeProducts(Product $product)
    {
        if (false === $this->products->contains($product)) {
            return;
        }
        $this->products->removeElement($product);
        $product->removeCustomerGroups($this);
    }

    /**
     * @param mixed products
     */
    public function addProducts(Product $product)
    {
        if (true === $this->products->contains($product)) {
            return;
        }
        $this->products->add($product);
        $product->addCustomerGroups($this);
    }


    /**
     * Get the value of Icon
     *
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set the value of Icon
     *
     * @param mixed icon
     *
     * @return self
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function removeIcon()
	{
		$this->setIcon(null);
	}

    /**
     * Get the value of Template Customer Groups
     *
     * @return mixed
     */
    public function getTemplateCustomerGroups()
    {
        return $this->templateCustomerGroups;
    }

    /**
     * Set the value of Template Customer Groups
     *
     * @param mixed templateCustomerGroups
     *
     * @return self
     */
    public function setTemplateCustomerGroups($templateCustomerGroups)
    {
        $this->templateCustomerGroups = $templateCustomerGroups;

        return $this;
    }

}
