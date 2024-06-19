<?php

namespace Pimcore\Model\DataObject\Product;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;

class Variation extends \Pimcore\Model\DataObject\Objectbrick {

protected $brickGetters = ['Variation'];


protected \Pimcore\Model\DataObject\Objectbrick\Data\Variation|null $Variation = null;

/**
* @return \Pimcore\Model\DataObject\Objectbrick\Data\Variation|null
*/
public function getVariation(bool $includeDeletedBricks = false)
{
	if(!$this->Variation && \Pimcore\Model\DataObject::doGetInheritedValues($this->getObject())) { 
		try {
			$brickContainer = $this->getObject()->getValueFromParent("variation");
			if(!empty($brickContainer)) {
				//check if parent object has brick, and if so, create an empty brick to enable inheritance
				$parentBrick = $this->getObject()->getValueFromParent("variation")->getVariation($includeDeletedBricks);
				if (!empty($parentBrick)) {
					$brickType = "\\Pimcore\\Model\\DataObject\\Objectbrick\\Data\\" . ucfirst($parentBrick->getType());
					$brick = new $brickType($this->getObject());
					$brick->setFieldname("variation");
					$this->setVariation($brick);
					return $brick;
				}
			}
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}
	if(!$includeDeletedBricks &&
		isset($this->Variation) &&
		$this->Variation->getDoDelete()) {
			return null;
	}
	return $this->Variation;
}

/**
* @param \Pimcore\Model\DataObject\Objectbrick\Data\Variation|null $Variation
* @return $this
*/
public function setVariation(?\Pimcore\Model\DataObject\Objectbrick\Data\Variation $Variation): static
{
	$this->Variation = $Variation;
	return $this;
}

}

