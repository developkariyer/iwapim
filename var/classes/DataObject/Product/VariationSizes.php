<?php

namespace Pimcore\Model\DataObject\Product;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;

class VariationSizes extends \Pimcore\Model\DataObject\Objectbrick {

protected $brickGetters = ['size'];


protected \Pimcore\Model\DataObject\Objectbrick\Data\Size|null $size = null;

/**
* @return \Pimcore\Model\DataObject\Objectbrick\Data\Size|null
*/
public function getSize(bool $includeDeletedBricks = false)
{
	if(!$this->size && \Pimcore\Model\DataObject::doGetInheritedValues($this->getObject())) { 
		try {
			$brickContainer = $this->getObject()->getValueFromParent("variationSizes");
			if(!empty($brickContainer)) {
				//check if parent object has brick, and if so, create an empty brick to enable inheritance
				$parentBrick = $this->getObject()->getValueFromParent("variationSizes")->getSize($includeDeletedBricks);
				if (!empty($parentBrick)) {
					$brickType = "\\Pimcore\\Model\\DataObject\\Objectbrick\\Data\\" . ucfirst($parentBrick->getType());
					$brick = new $brickType($this->getObject());
					$brick->setFieldname("variationSizes");
					$this->setSize($brick);
					return $brick;
				}
			}
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}
	if(!$includeDeletedBricks &&
		isset($this->size) &&
		$this->size->getDoDelete()) {
			return null;
	}
	return $this->size;
}

/**
* @param \Pimcore\Model\DataObject\Objectbrick\Data\Size|null $size
* @return $this
*/
public function setSize(?\Pimcore\Model\DataObject\Objectbrick\Data\Size $size): static
{
	$this->size = $size;
	return $this;
}

}

