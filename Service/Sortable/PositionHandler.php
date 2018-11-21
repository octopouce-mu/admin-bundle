<?php
/**
 * Created by Kévin Hilairet <kevin@octopouce.mu>
 * Date: 21/11/2018
 */

namespace Octopouce\AdminBundle\Service\Sortable;


abstract class PositionHandler
{
	protected $positionField;

	abstract public function setPosition($entity, $setter, $dataPositionList);

	/**
	 * @param mixed $positionField
	 */
	public function setPositionField($positionField)
	{
		$this->positionField = $positionField;
	}

	/**
	 * @param $entity
	 *
	 * @return string
	 */
	public function getPositionFieldByEntity($entity)
	{
		if (is_object($entity)) {
			$entity = \Doctrine\Common\Util\ClassUtils::getClass($entity);
		}

		if (isset($this->positionField['entities'][$entity])) {
			return $this->positionField['entities'][$entity];
		} else {
			return $this->positionField['default'];
		}
	}
}