<?php

namespace Pimcore\Model\DataObject\Marketplace;

use Pimcore\Model\DataObject\Exception\InheritanceParentNotFoundException;

class Marketplace extends \Pimcore\Model\DataObject\Objectbrick {

protected $brickGetters = ['amazonbrick','etsybrick','shopifybrick','trendyolbrick'];


protected \Pimcore\Model\DataObject\Objectbrick\Data\Amazonbrick|null $amazonbrick = null;

/**
* @return \Pimcore\Model\DataObject\Objectbrick\Data\Amazonbrick|null
*/
public function getAmazonbrick(bool $includeDeletedBricks = false)
{
	if(!$includeDeletedBricks &&
		isset($this->amazonbrick) &&
		$this->amazonbrick->getDoDelete()) {
			return null;
	}
	return $this->amazonbrick;
}

/**
* @param \Pimcore\Model\DataObject\Objectbrick\Data\Amazonbrick|null $amazonbrick
* @return $this
*/
public function setAmazonbrick(?\Pimcore\Model\DataObject\Objectbrick\Data\Amazonbrick $amazonbrick): static
{
	$this->amazonbrick = $amazonbrick;
	return $this;
}

protected \Pimcore\Model\DataObject\Objectbrick\Data\Etsybrick|null $etsybrick = null;

/**
* @return \Pimcore\Model\DataObject\Objectbrick\Data\Etsybrick|null
*/
public function getEtsybrick(bool $includeDeletedBricks = false)
{
	if(!$includeDeletedBricks &&
		isset($this->etsybrick) &&
		$this->etsybrick->getDoDelete()) {
			return null;
	}
	return $this->etsybrick;
}

/**
* @param \Pimcore\Model\DataObject\Objectbrick\Data\Etsybrick|null $etsybrick
* @return $this
*/
public function setEtsybrick(?\Pimcore\Model\DataObject\Objectbrick\Data\Etsybrick $etsybrick): static
{
	$this->etsybrick = $etsybrick;
	return $this;
}

protected \Pimcore\Model\DataObject\Objectbrick\Data\Shopifybrick|null $shopifybrick = null;

/**
* @return \Pimcore\Model\DataObject\Objectbrick\Data\Shopifybrick|null
*/
public function getShopifybrick(bool $includeDeletedBricks = false)
{
	if(!$includeDeletedBricks &&
		isset($this->shopifybrick) &&
		$this->shopifybrick->getDoDelete()) {
			return null;
	}
	return $this->shopifybrick;
}

/**
* @param \Pimcore\Model\DataObject\Objectbrick\Data\Shopifybrick|null $shopifybrick
* @return $this
*/
public function setShopifybrick(?\Pimcore\Model\DataObject\Objectbrick\Data\Shopifybrick $shopifybrick): static
{
	$this->shopifybrick = $shopifybrick;
	return $this;
}

protected \Pimcore\Model\DataObject\Objectbrick\Data\Trendyolbrick|null $trendyolbrick = null;

/**
* @return \Pimcore\Model\DataObject\Objectbrick\Data\Trendyolbrick|null
*/
public function getTrendyolbrick(bool $includeDeletedBricks = false)
{
	if(!$includeDeletedBricks &&
		isset($this->trendyolbrick) &&
		$this->trendyolbrick->getDoDelete()) {
			return null;
	}
	return $this->trendyolbrick;
}

/**
* @param \Pimcore\Model\DataObject\Objectbrick\Data\Trendyolbrick|null $trendyolbrick
* @return $this
*/
public function setTrendyolbrick(?\Pimcore\Model\DataObject\Objectbrick\Data\Trendyolbrick $trendyolbrick): static
{
	$this->trendyolbrick = $trendyolbrick;
	return $this;
}

}

