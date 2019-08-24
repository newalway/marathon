<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * PortfolioTranslation
 *
 * @ORM\Table(name="portfolio_translation")
 * @ORM\Entity(repositoryClass="ProjectBundle\Repository\PortfolioTranslationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PortfolioTranslation
{
    use ORMBehaviors\Translatable\Translation;
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="short_desc", type="text", length=65535, nullable = true)
     */
    private $shortDesc;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=65535, nullable = true)
     */
    private $description;

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
     * Get the value of Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param string title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of Short Desc
     *
     * @return string
     */
    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    /**
     * Set the value of Short Desc
     *
     * @param string shortDesc
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of Description
     *
     * @param string description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

}
