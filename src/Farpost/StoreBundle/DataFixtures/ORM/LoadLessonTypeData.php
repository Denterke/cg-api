<?php
namespace Farpost\StoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Farpost\StoreBundle\Entity\LessonType;

class LoadLessonTypeData implements FixtureInterface
{
	public function load(ObjectManager $manager)
	{
		$nId = LessonType::$NOTHING_TYPE_ID;
		$nAlias = LessonType::$NOTHING_TYPE_ALIAS;
		$sql = "INSERT INTO lesson_types (id, alias) VALUES ($nId, '$nAlias')";
		$stmt = $manager->getConnection()->prepare($sql);
		$fakeType = $manager->getRepository('FarpostStoreBundle:LessonType')
			->findOneBy(['id' => LessonType::$NOTHING_TYPE_ID]);
		if (is_null($fakeType)) {
			$stmt->execute();
			return;
		} else {
			$fakeType->setAlias(LessonType::$NOTHING_TYPE_ALIAS);
			$manager->merge($fakeType);
			$manager->flush();
		}
	}
}
