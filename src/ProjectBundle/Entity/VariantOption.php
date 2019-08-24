<?php

namespace ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * VariantOption
 *
 * @ORM\Table(name="variant_option", indexes={@ORM\Index(name="search_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\VariantOptionRepository")
 */
class VariantOption
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
     * @ORM\ManyToOne(targetEntity="Variant", inversedBy="variantOptions")
     * @ORM\JoinColumn(name="variant_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $variant;

    /**
     * @ORM\OneToMany(targetEntity="SkuValue", mappedBy="variantOption")
     */
    private $skuValues;

    /**
     * @ORM\Column(name="product_id", type="integer", nullable = true)
     */
	private $productId;

    public function __construct()
    {
      $this->skuValues = new ArrayCollection();
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
     * Get the value of Variant
     *
     * @return mixed
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * Set the value of Variant
     *
     * @param mixed variant
     *
     * @return self
     */
    public function setVariant($variant)
    {
        $this->variant = $variant;

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

    /**
     * Get the value of Product Id
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set the value of Product Id
     *
     * @param mixed productId
     *
     * @return self
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

}
