<?php
namespace Farpost\TestBundle\Entity;

class SimpleForm
{
	protected $stype;

	public function getStype()
	{
		return $this->stype;
	}

	public function setStype($stype)
	{
		$this->stype = $stype;
	}
}
