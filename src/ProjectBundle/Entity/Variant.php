<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Variant
 *
 * @ORM\Table(name="variant", indexes={@ORM\Index(name="search_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\VariantRepository")
 */
class Variant
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
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $name;

    /**
	 * @ORM\ManyToOne(targetEntity="Product", inversedBy="variants")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $product;

    /**
     * @ORM\OneToMany(targetEntity="SkuValue", mappedBy="variant")
     */
    private $skuValues;

    /**
     * @ORM\OneToMany(targetEntity="VariantOption", mappedBy="variant")
     */
    private $variantOptions;

    public function __construct()
    {
      $this->skuValues = new ArrayCollection();
      $this->variantOptions = new ArrayCollection();
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
     * Get the value of Product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set the value of Product
     *
     * @param mixed product
     *
     * @return self
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get the value of Variant Options
     *
     * @return mixed
     */
    public function getVariantOptions()
    {
        return $this->variantOptions;
    }

    /**
     * Set the value of Variant Options
     *
     * @param mixed variantOptions
     *
     * @return self
     */
    public function setVariantOptions($variantOptions)
    {
        $this->variantOptions = $variantOptions;

        return $this;
    }

    /**
     * Get the value of Sku Values
     *
     * @return mixed
     */
    public function getSkuValues()
    {
        return $this->skuValues;
    }

    /**
     * Set the value of Sku Values
     *
     * @param mixed skuValues
     *
     * @return self
     */
    public function setSkuValues($skuValues)
    {
        $this->skuValues = $skuValues;

        return $this;
    }

}
