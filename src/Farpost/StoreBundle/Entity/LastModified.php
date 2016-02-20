<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LastModified
 *
 * @ORM\Table(name="last_modified")
 * @ORM\Entity
 */
class LastModified
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="table_name", type="string")
     */
    protected $table_name;

    /**
     * @var integer
     *
     * @ORM\Column(name="record_id", type="integer")
     */
    protected $record_id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified", type="datetime")
     *
    */
    protected $last_modified;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    protected $status;

    /**
     * @var Group
     *
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     */
    protected $group_id;

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
     * Set table_name
     *
     * @param string $tableName
     * @return LastModified
     */
    public function setTableName($tableName)
    {
        $this->table_name = $tableName;

        return $this;
    }

    /**
     * Get table_name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * Set record_id
     *
     * @param integer $recordId
     * @return LastModified
     */
    public function setRecordId($recordId)
    {
        $this->record_id = $recordId;

        return $this;
    }

    /**
     * Get record_id
     *
     * @return integer
     */
    public function getRecordId()
    {
        return $this->record_id;
    }

    /**
     * Set last_modified
     *
     * @param \DateTime $lastModified
     * @return LastModified
     */
    public function setLastModified($lastModified)
    {
        $this->last_modified = $lastModified;

        return $this;
    }

    /**
     * Get last_modified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->last_modified;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return LastModified
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set group_id
     *
     * @param integer $groupId
     * @return LastModified
     */
    public function setGroupId($groupId)
    {
        $this->group_id = $groupId;

        return $this;
    }

    /**
     * Get group_id
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->group_id;
    }
}
