<?php

namespace App\Model\DataObject;

use App\Utils\PdfGenerator;
use Exception;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Folder;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Product\Listing;
use App\Utils\Utility;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\DataObject\Data\Video;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\Element\DuplicateFullPathException;

/**
 * Class User
 *
 * This class serves as a data object for managing
 * user data
 *
 * @package App\Model\DataObject
 */
class User extends Concrete
{
    public function getUsername(): ?string
    {
        return $this->getUsername();
    }

    public function getUserIdentifier(): string
    {
        return $this->getUserIdentifier();
    }
}