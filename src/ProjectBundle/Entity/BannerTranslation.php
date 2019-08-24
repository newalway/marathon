<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Table(name="banner_translation", indexes={@ORM\Index(name="search_idx", columns={"title"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class BannerTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $subtitle;

    /**
     * @ORM\Column(type="text", length=65535, nullable = true)
     */
    private $website;


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
     * Get the value of Subtitle
     *
     * @return mixed
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set the value of Subtitle
     *
     * @param mixed subtitle
     *
     * @return self
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }


    /**
     * Get the value of Website
     *
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set the value of Website
     *
     * @param mixed website
     *
     * @return self
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

}
