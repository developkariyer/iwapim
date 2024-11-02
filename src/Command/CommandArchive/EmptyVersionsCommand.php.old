<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\Version;


#[AsCommand(
    name: 'app:empty-versions',
    description: 'List and delete empty versions of all objects in the system.'
)]
class EmptyVersionsCommand extends AbstractCommand
{

    private function compareObjects($objectA, $objectB)
    {
        // compare objects objectA and objectB but ignore changes in the following fields: modificationDate, versionCount
        $ignoreFields = ['modificationDate', 'versionCount'];
        $dataA = json_decode(json_encode($objectA->getData()), true);
        $dataB = json_decode(json_encode($objectB->getData()), true);
        foreach ($ignoreFields as $field) {
            unset($dataA[$field]);
            unset($dataB[$field]);
        }
        return $dataA == $dataB;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $objects = new DataObject\Listing();
        foreach ($objects as $object) {
            $versions = new Version\Listing();
            $versions->setCondition('cid = ? AND ctype = ?', [$object->getId(), 'object']);
            $previousVersion = null;

            echo "Processing {$versions->count()} versions for object ID {$object->getId()}\n";
            foreach ($versions as $version) {
                if ($previousVersion === null) {
                    $previousVersion = $version;
                } else {
                    if (self::compareObjects($version, $previousVersion)) {
                        // No changes between this version and the previous one, delete it
                        $version->delete();
                        echo "    Deleted version ID {$version->getId()} for object ID {$object->getId()}\n";
                        exit;
                    } else {
                        $previousVersion = $version;
                    }
                }
            }
        }

        $output->writeln("Cleanup completed.");
        
        return 0; // Return 0 to indicate the command executed successfully
    }
}
