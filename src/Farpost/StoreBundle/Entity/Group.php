<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity
 */
class Group
{
   /**
    * @var integer
    *
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="IDENTITY")
    */
   protected $id;

   /**
    * @var string
    *
    * @ORM\Column(name="alias", type="string", length=255)
    */
   protected $alias;

   /**
    * @var StudySet
    *
    * @ORM\ManyToOne(targetEntity="StudySet")
    * @ORM\JoinColumn(name="study_set_id", referencedColumnName="id", onDelete="CASCADE")
    */
   protected $study_set;


   /**
    * Get id
    *
    * @return integer
    */
   public function getId()
   {
      return $this->id;
   }

   /**
    * Set alias
    *
    * @param string $alias
    * @return Groups
    */
   public function setAlias($alias)
   {
      $this->alias = $alias;
      return $this;
   }

   /**
    * Get alias
    *
    * @return string
    */
   public function getAlias()
   {
      return $this->alias;
   }

    /**
     * Set study_set
     *
     * @param \Farpost\StoreBundle\Entity\StudySet $study_set
     * @return Group
     */
    public function setStudySet(\Farpost\StoreBundle\Entity\StudySet $study_set = null)
    {
        $this->study_set = $study_set;

        return $this;
    }

    /**
     * Get study_set
     *
     * @return \Farpost\StoreBundle\Entity\StudySet
     */
    public function getStudySet()
    {
        return $this->study_set;
    }
}
