<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Table(name="customer_group_translation", indexes={@ORM\Index(name="search_idx", columns={"title"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class CustomerGroupTranslation
{
    use ORMBehaviors\Translatable\Translation;

    /**
    * @var string
    *
    * @ORM\Column(type="string", length=255)
    */
    private $title;

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

}
