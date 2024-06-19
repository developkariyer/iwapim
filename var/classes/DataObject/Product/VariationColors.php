<?php

namespace Pimcore\Model\DataObject\Product;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;

class VariationColors extends \Pimcore\Model\DataObject\Objectbrick {

protected $brickGetters = ['color'];


protected \Pimcore\Model\DataObject\Objectbrick\Data\Color|null $color = null;

/**
* @return \Pimcore\Model\DataObject\Objectbrick\Data\Color|null
*/
public function getColor(bool $includeDeletedBricks = false)
{
	if(!$this->color && \Pimcore\Model\DataObject::doGetInheritedValues($this->getObject())) { 
		try {
			$brickContainer = $this->getObject()->getValueFromParent("variationColors");
			if(!empty($brickContainer)) {
				//check if parent object has brick, and if so, create an empty brick to enable inheritance
				$parentBrick = $this->getObject()->getValueFromParent("variationColors")->getColor($includeDeletedBricks);
				if (!empty($parentBrick)) {
					$brickType = "\\Pimcore\\Model\\DataObject\\Objectbrick\\Data\\" . ucfirst($parentBrick->getType());
					$brick = new $brickType($this->getObject());
					$brick->setFieldname("variationColors");
					$this->setColor($brick);
					return $brick;
				}
			}
		} catch (InheritanceParentNotFoundException $e) {
			// no data from parent available, continue ...
		}
	}
	if(!$includeDeletedBricks &&
		isset($this->color) &&
		$this->color->getDoDelete()) {
			return null;
	}
	return $this->color;
}

/**
* @param \Pimcore\Model\DataObject\Objectbrick\Data\Color|null $color
* @return $this
*/
public function setColor(?\Pimcore\Model\DataObject\Objectbrick\Data\Color $color): static
{
	$this->color = $color;
	return $this;
}

}

