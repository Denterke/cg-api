<?php

namespace Farpost\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User
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
    * @ORM\Column(name="first_name", type="string", length=255)
    */
   protected $first_name;

   /**
    * @var string
    *
    * @ORM\Column(name="last_name", type="string", length=255)
    */
   protected $last_name;

   /**
    * @var string
    *
    * @ORM\Column(name="middle_name", type="string", length=255)
    */
   protected $middle_name;

   /**
    * @var string
    *
    * @ORM\Column(name="pass_md5", type="string", length=255)
    */
   protected $pass_md5;

   /**
    * @var string
    *
    * @ORM\Column(name="login", type="string", length=255, unique=true)
    */
   protected $login;

   /**
    * @var string
    *
    * @ORM\Column(name="salt", type="string", length=255)
    */
   protected $salt;

   /**
    * @ORM\ManyToMany(targetEntity="Role")
    * @ORM\JoinTable(name="users_roles",
    * joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
    * inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
    * )
    */
   protected $roles;

   public function __construct()
   {
      $roles = new ArrayCollection();
   }


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
    * @return Users
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
    * Set first_name
    *
    * @param string $firstName
    * @return User
    */
   public function setFirstName($firstName)
   {
      $this->first_name = $firstName;
      return $this;
   }

   /**
    * Get first_name
    *
    * @return string
    */
   public function getFirstName()
   {
      return $this->first_name;
   }

   /**
    * Set last_name
    *
    * @param string $lastName
    * @return User
    */
   public function setLastName($lastName)
   {
      $this->last_name = $lastName;
      return $this;
   }

   /**
    * Get last_name
    *
    * @return string
    */
   public function getLastName()
   {
      return $this->last_name;
   }

   /**
    * Set middle_name
    *
    * @param string $middleName
    * @return User
    */
   public function setMiddleName($middleName)
   {
      $this->middle_name = $middleName;
      return $this;
   }

   /**
    * Get middle_name
    *
    * @return string
    */
   public function getMiddleName()
   {
      return $this->middle_name;
   }

   /**
    * Set pass_md5
    *
    * @param string $passMd5
    * @return User
    */
   public function setPassMd5($passMd5)
   {
      $this->pass_md5 = $passMd5;
      return $this;
   }

   /**
    * Get pass_md5
    *
    * @return string
    */
   public function getPassMd5()
   {
      return $this->pass_md5;
   }

   /**
    * Set login
    *
    * @param string $login
    * @return User
    */
   public function setLogin($login)
   {
      $this->login = $login;
      return $this;
   }

   /**
    * Get login
    *
    * @return string
    */
   public function getLogin()
   {
      return $this->login;
   }

   /**
    * Set salt
    *
    * @param string $salt
    * @return User
    */
   public function setSalt($salt)
   {
      $this->salt = $salt;
      return $this;
   }

   /**
    * Get salt
    *
    * @return string
    */
   public function getSalt()
   {
      return $this->salt;
   }

   /**
    * Add roles
    *
    * @param \Farpost\StoreBundle\Entity\Role $roles
    * @return User
    */
   public function addRole(\Farpost\StoreBundle\Entity\Role $roles)
   {
      $this->roles[] = $roles;
      return $this;
   }

   /**
    * Remove roles
    *
    * @param \Farpost\StoreBundle\Entity\Role $roles
    */
   public function removeRole(\Farpost\StoreBundle\Entity\Role $roles)
   {
      $this->roles->removeElement($roles);
   }

   /**
    * Get roles
    *
    * @return \Doctrine\Common\Collections\Collection
    */
   public function getRoles()
   {
      return $this->roles;
   }
}
