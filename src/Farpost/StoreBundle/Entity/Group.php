<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="groups")
 * @ORM\Entity(repositoryClass="Farpost\StoreBundle\Entity\GroupRepository")
 */
class Group
{
   const MAX_SCH_RENDERED_COUNT = 3000;
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
     * @var integer
     *
     * @ORM\Column(name="max_count", type="integer", nullable=true)
     */
    protected $maxCount; //max achieved schedule rendered count.

    /**
     * @var timestamp
     *
     * @ORM\Column(name="last_modified", type="integer")
     */
    protected $lastModified;

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

    /**
     * Set maxCount
     *
     * @param integer $maxCount
     * @return Group
     */
    public function setMaxCount($maxCount)
    {
        $this->maxCount = $maxCount;
        return $this;
    }

    /**
     * Get maxCount
     *
     * @return integer 
     */
    public function getMaxCount()
    {
        return $this->maxCount;
    }

    /**
     * Set lastModified
     *
     * @param integer $lastModified
     * @return Group
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return integer 
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Get first id for schedule rendered, connected for this group
     * Added: [2.0]
     * @param integer $offset
     * @return integer
     */
    public function getSRFirstId($offset = 0)
    {
        return ($this->id + $offset) * self::MAX_SCH_RENDERED_COUNT;
    }
}
