<?php

namespace Pimcore\Model\DataObject\PricingNode;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;

class AirDeci extends \Pimcore\Model\DataObject\Objectbrick {

protected $brickGetters = ['variableAirDeci'];


protected \Pimcore\Model\DataObject\Objectbrick\Data\VariableAirDeci|null $variableAirDeci = null;

/**
* @return \Pimcore\Model\DataObject\Objectbrick\Data\VariableAirDeci|null
*/
public function getVariableAirDeci(bool $includeDeletedBricks = false)
{
	if(!$this->variableAirDeci && \Pimcore\Model\DataObject::doGetInheritedValues($this->getObject())) { 
		try {
			$brickContainer = $this->getObject()->getValueFromParent("airDeci");
			if(!empty($brickContainer)) {
				//check if parent object has brick, and if so, create an empty brick to enable inheritance
				$parentBrick = $this->getObject()->getValueFromParent("airDeci")->getVariableAirDeci($includeDeletedBricks);
				if (!empty($parentBrick)) {
					$brickType = "\\Pimcore\\Model\\DataObject\\Objectbrick\\Data\\" . ucfirst($parentBrick->getType());
					$brick = new $brickType($this->getObject());
					$brick->setFieldname("airDeci");
					$this->setVariableAirDeci($brick);
					return $brick;
				}
			}
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}
	if(!$includeDeletedBricks &&
		isset($this->variableAirDeci) &&
		$this->variableAirDeci->getDoDelete()) {
			return null;
	}
	return $this->variableAirDeci;
}

/**
* @param \Pimcore\Model\DataObject\Objectbrick\Data\VariableAirDeci|null $variableAirDeci
* @return $this
*/
public function setVariableAirDeci(?\Pimcore\Model\DataObject\Objectbrick\Data\VariableAirDeci $variableAirDeci): static
{
	$this->variableAirDeci = $variableAirDeci;
	return $this;
}

}

