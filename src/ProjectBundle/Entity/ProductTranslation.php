<?php

namespace ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
* @ORM\Table(name="product_translation", indexes={@ORM\Index(name="search_idx", columns={"title"})})
* @ORM\Entity
* @ORM\HasLifecycleCallbacks
*/
class ProductTranslation
{
	use ORMBehaviors\Translatable\Translation;

	/**
	* @var string
	*
	* @ORM\Column(type="string", length=255)
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
     * @ORM\PostUpdate
     * @ORM\PostPersist
     */
    public function updateSlug(LifecycleEventArgs $eventArgs)
    {
        // updateSlug for different slug entity (title in ProductTranslation, slug in Product)
        // slug updated when updating translation
        $translatable = $this->getTranslatable();
        $oldSlug = $translatable->getSlug();
        $translatable->generateSlug();
        $newSlug = $translatable->getSlug();

        if ($oldSlug !== $newSlug) {
            $id = $translatable->getId();
            $em = $eventArgs->getEntityManager();
            $data = $em->getRepository(get_class($translatable))->find($id);
            $data->setSlug($newSlug);
            $em->flush();
            // $repo = $eventArgs->getEntityManager()->getRepository(get_class($translatable));
            // $repo->setSlug($translatable);
        }
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
