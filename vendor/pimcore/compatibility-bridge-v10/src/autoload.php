<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

/**
 * TODO: BC layer, remove with Pimcore 12
 */
$classAliases = [
    '\Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger' => '\Pimcore\Log\ApplicationLogger',
    '\Pimcore\Bundle\ApplicationLoggerBundle\Controller\LogController' => '\Pimcore\Bundle\AdminBundle\Controller\Admin\ApplicationLoggerDb',
    '\Pimcore\Bundle\ApplicationLoggerBundle\FileObject' => '\Pimcore\Log\FileObject',
    '\Pimcore\Bundle\ApplicationLoggerBundle\Handler\ApplicationLoggerDb' => '\Pimcore\Log\Handler\ApplicationLoggerDb',
    '\Pimcore\Bundle\ApplicationLoggerBundle\Maintenance\LogArchiveTask' => '\Pimcore\Maintenance\Tasks\LogArchiveTask',
    '\Pimcore\Bundle\ApplicationLoggerBundle\Maintenance\LogMailMaintenanceTask' => '\Pimcore\Maintenance\Tasks\LogMailMaintenanceTask',
    '\Pimcore\Bundle\ApplicationLoggerBundle\Processor\ApplicationLoggerProcessor' => '\Pimcore\Log\Processor\ApplicationLoggerProcessor',
    '\Pimcore\Bundle\PersonalizationBundle\Model\Tool\Targeting\TargetGroup' => '\Pimcore\Model\Tool\Targeting\TargetGroup',
    '\Pimcore\Model\DataObject\Data\GeoCoordinates' => '\Pimcore\Model\DataObject\Data\Geopoint',
    '\Pimcore\Bundle\AdminBundle\DataObject\GridColumnConfig\ConfigElementInterface' => '\Pimcore\DataObject\GridColumnConfig\ConfigElementInterface',
    '\Pimcore\Bundle\AdminBundle\Perspective\Config' => '\Pimcore\Perspective\Config',
    '\Pimcore\Bundle\AdminBundle\CustomView\Config' => '\Pimcore\CustomView\Config',
    '\Pimcore\Bundle\AdminBundle\Service\Workflow\ActionsButtonService' => '\Pimcore\Workflow\ActionsButtonService',
    '\Pimcore\Bundle\NewsletterBundle\Model\DataObject\ClassDefinition\Data\NewsletterConfirmed' => '\Pimcore\Model\DataObject\ClassDefinition\Data\NewsletterConfirmed',
    '\Pimcore\Bundle\NewsletterBundle\Model\DataObject\ClassDefinition\Data\NewsletterActive' => '\Pimcore\Model\DataObject\ClassDefinition\Data\NewsletterActive',
];

foreach ($classAliases as $class => $alias) {
    if (!class_exists($alias, false)) {
        @class_alias($class, $alias);
    }
}
