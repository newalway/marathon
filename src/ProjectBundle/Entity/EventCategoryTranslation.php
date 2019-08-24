<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Table(name="event_category_translation", indexes={@ORM\Index(name="search_idx", columns={"title"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class EventCategoryTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
     * @ORM\Column(type="string", length=255, nullable = true)
     */
    private $title;

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

}
