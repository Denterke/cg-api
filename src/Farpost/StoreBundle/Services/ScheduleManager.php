<?php
namespace Farpost\StoreBundle\Services;

use Farpost\StoreBundle\Entity\ScheduleSource;
use Symfony\Component\Process\Process;
use Farpost\StoreBundle\Classes\GroupCache;

class ScheduleManager {
	private $doctrine;
	private $session;
   private $groupCache;

	public function startAstarot() {
		exec(WEB_DIRECTORY . "/../app/console astarot > /dev/null 2>&1 &");
		// $process = new Process("php " . );
		// $process->start();
	}

	private function logWrite($str) {
		$dt = new \DateTime;
		error_log($dt->format('H:i:s:u') . ">>$str");
	}

	public function __construct($doctrine) {
		$this->doctrine = $doctrine;
      $this->groupCache = new GroupCache($this->doctrine->getConnection());
      error_log($this->groupCache->statistics());
	}

	public function generateSchedule($schedule) {
		$pdo = $this->doctrine->getConnection();
		$current_time = \DateTime::createFromFormat('Y-m-d', $schedule['time_start']);
		$dow = $schedule['day'];
		$current_dow = date("N", $current_time->getTimestamp());
		$period = $schedule['period'];
		$end_time = \DateTime::createFromFormat('Y-m-d', $schedule['time_end']);
		if ($dow < $current_dow) {
			$dow += $period;
			$current_time = $current_time->add(new \DateInterval('P' . $period . 'D'));
		}
		while ($dow != $current_dow) {
			$current_dow++;
			$current_time = $current_time->add(new \DateInterval('P'. 1 . 'D'));
		}
		$stmt = $pdo->prepare('SELECT * FROM renderSchedule(:s_id, :period, :start_time, :end_time);');
		$stmt->bindValue(':s_id', $schedule['id'], \PDO::PARAM_INT);
		$stmt->bindValue(':period', "$period day", \PDO::PARAM_STR);
		$stmt->bindValue(':start_time', $current_time->format('Y-m-d'), \PDO::PARAM_STR);
		$stmt->bindValue(':end_time', $end_time->format('Y-m-d') . \PDO::PARAM_STR);
		$stmt->execute();
	}

	private function syncGroupInfo($group_info) {
		$em = $this->doctrine->getManager('default');
		try {
			// echo $group_info;
			list(
				$_school,
				$_group,
				$_study_type,
				$_course,
				$_department,
				$_spec
			) = explode(";", $group_info);
			// $group_str_refs = explode(";", $group_info);
		}
		 catch (\Exception $e) {
			throw new \Exception("Can not split group info string:\n$group_info\n " . $e->getMessage());
		}
		// $stmt = $em->getConnection()->prepare('SELECT * FROM groupInfoSync(:school, :st, :spec, :course, :dep, :group)');
		// $stmt->bindValue(':school', $_school, \PDO::PARAM_STR);
		// $stmt->bindValue(':st', $_study_type, \PDO::PARAM_STR);
		// $stmt->bindValue(':spec', $_spec, \PDO::PARAM_STR);
		// $stmt->bindValue(':course', $_course, \PDO::PARAM_STR);
		// $stmt->bindValue(':dep', $_department, \PDO::PARAM_STR);
		// $stmt->bindValue(':group', $_group, \PDO::PARAM_STR);
		// $stmt->execute();
		// $groupId = $stmt->fetch()['groupinfosync'];
      $groupId = $this->groupCache->syncGroupInfo(
         $_school,
         $_study_type,
         $_spec,
         $_course,
         $_department,
         $_group
      );
      error_log($this->groupCache->statistics());
		return $groupId;
	}

	public function convertSchedule($path, $vdatetime, $createSS = true) {
		$this->logWrite('converter, step: 1');
		$em = $this->doctrine->getManager('default');
		$group_info_entities = ['School', 'Group', 'StudyType', 'Course', 'Department', 'Specialization'];
		$ss_file = fopen($path, 'r');
		if (!$ss_file) {
			throw new \Exception("Can not open file: <$path>");
		}
		$group_info = fgets($ss_file);
		$str_num = 1;
		$this->logWrite('converter, step: 2');
		$gId = $this->syncGroupInfo($group_info);
		$this->logWrite('converter, step: 3');
		$templates = [];
		$fake = [
			'geo' => [],
			'time' => [],
			'ltype' => [],
			'disc' => [],
			'sp' => [],
			'user' => []

		];
		$insStr =
		"INSERT INTO
            schedule
            (schedule_part_id, auditory_id, time_id, lesson_type_id, semester_id, period, day, status)
          VALUES ";
		$this->logWrite('converter, step: 4');
		while (!feof($ss_file)) {
			$schedule_template = fgets($ss_file);
			if (rtrim($schedule_template) == '') {
				break;
			}
			try {
				list(
					$_l_num,
					$_discipline,
					$_l_type,
					$_professor,
					$_geoobject,
					$period,
					$day
				) = explode(";", $schedule_template);
			}
			 catch (\Exception $e) {
				throw new \Exception("Can not split schedule template string #$str_num: " . $e->getMessage());
			}
			$_professor = rtrim($_professor);
			$discIdx = array_search($_discipline, $fake['disc']);
			if ($discIdx === false) {
				$discIdx = array_push($fake['disc'], $_discipline) - 1;
			}
			if ($_professor) {
				$userIdx = array_search($_professor, $fake['user']);
				if ($userIdx === false) {
					$userIdx = array_push($fake['user'], $_professor) - 1;
				}
			}
			$_sp = ['user' => $userIdx, 'disc' => $discIdx, 'group' => $gId];
			$spIdx = array_search($_sp, $fake['sp']);
			if ($spIdx === false) {
				$spIdx = array_push($fake['sp'], $_sp) - 1;
			}
			$geoIdx = array_search($_geoobject, $fake['geo']);
			if ($geoIdx === false) {
				$geoIdx = array_push($fake['geo'], $_geoobject) - 1;
			}
			$ltIdx = array_search($_l_type, $fake['ltype']);
			if ($ltIdx === false) {
				$ltIdx = array_push($fake['ltype'], $_l_type) - 1;
			}
			$timeIdx = array_search($_l_num, $fake['time']);
			if ($timeIdx === false) {
				$timeIdx = array_push($fake['time'], $_l_num) - 1;
			}
			if ($day == '' || $period == '') {
				throw new \Exception("day or period is null");
			}
			array_push(
				$templates,
				[
					'day' => $day,
					'period' => $period,
					'geo' => $geoIdx,
					'sp' => $spIdx,
					'ltype' => $ltIdx,
					'time' => $timeIdx
				]
			);
		}
		$entities = [
			'geo' => 'GeoObject',
			'time' => 'Time',
			'ltype' => 'LessonType',
			'disc' => 'Discipline',
			'user' => 'User'
		];
		$this->logWrite('converter, step: 6');
		foreach ($entities as $key => $entity) {
			$em->getRepository('FarpostStoreBundle:' . $entity)->realizeFake($fake[$key]);
		}
		$this->logWrite('converter, step: 7');
		foreach ($fake['sp'] as &$sp) {
			$sp['user'] = $fake['user'][$sp['user']];
			$sp['disc'] = $fake['disc'][$sp['disc']];
		}
		$em->getRepository('FarpostStoreBundle:SchedulePart')
		   ->realizeFake($fake['sp'], $gId);
		$this->logWrite('converter, step: 8');
		$firstIns = true;
		$this->logWrite('converter, step: 9');
		foreach ($templates as &$t) {
			$insStr .= $firstIns ? ' ' : ', ';
			$firstIns = false;
			try {
				$insStr .= "({$fake['sp'][$t['sp']]}, {$fake['geo'][$t['geo']]}, " .
				"{$fake['time'][$t['time']]}, {$fake['ltype'][$t['ltype']]}, " .
				"1, {$t['period']}, {$t['day']}, 0)";
			} catch (\Exception $e) {
				print_r($fake);
				echo "<p>.........................</p>";
				print_r($t);
				throw $e;
			}
		}
		$this->logWrite('converter, step: 10');
		if (!$firstIns) {
			$insStr .= " returning id";
			$pdo = $em->getConnection();
			$stmt = $pdo->prepare($insStr);
			$this->logWrite('converter, step: 11');
			$stmt->execute();
			$ids = $stmt->fetchAll();
			$this->logWrite('converter, step: 12');
		}
		if ($createSS) {
			$ssource = new ScheduleSource();
			$ssource->setVDatetime($vdatetime)
			        ->setBase($path)
			        ->setGroup($em->getRepository('FarpostStoreBundle:Group')->findOneById($gId))
			->cpFile();
			$em->persist($ssource);
			$em->flush();
		}
	}

	public function refreshSchedule() {
		$schedule_templates = $this->doctrine->getManager('default')
		                           ->getRepository('FarpostStoreBundle:ScheduleSource', 'ssrc')
		                           ->getLastRecords();
		foreach ($schedule_templates as &$s_template) {
			$this->convertSchedule(
				$s_template->getBase(),
				$s_template->getVDatetime(),
				false
			);
		}
		$this->startAstarot();
	}
}
