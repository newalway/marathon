<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Table(name="promotion_translation")
 * @ORM\Entity
 */
class PromotionTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
    * @ORM\Column(type="string", length=255, nullable = true)
    */
    private $title;

    /**
    * @ORM\Column(type="string", length=255, nullable = true)
    */
    private $image;

    /**
    * @ORM\Column(name="short_desc", type="text", length=65535, nullable = true)
    */
    private $shortDesc;

    /**
    * @ORM\Column(type="text", length=65535, nullable = true)
    */
    private $description;


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

    public function removeImage()
	{
		$this->setImage(null);
	}


    /**
     * Get the value of Short Desc
     *
     * @return mixed
     */
    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    /**
     * Set the value of Short Desc
     *
     * @param mixed shortDesc
     *
     * @return self
     */
    public function setShortDesc($shortDesc)
    {
        $this->shortDesc = $shortDesc;

        return $this;
    }

    /**
     * Get the value of Description
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of Description
     *
     * @param mixed description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

}
