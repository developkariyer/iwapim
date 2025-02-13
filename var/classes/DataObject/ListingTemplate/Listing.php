<?php

namespace Pimcore\Model\DataObject\ListingTemplate;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\ListingTemplate|false current()
 * @method DataObject\ListingTemplate[] load()
 * @method DataObject\ListingTemplate[] getData()
 * @method DataObject\ListingTemplate[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "listingTemplate";
protected $className = "ListingTemplate";


/**
* Filter by marketplace (Market)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByMarketplace ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("marketplace")->addListingFilter($this, $data, $operator);
	return $this;
}



}
