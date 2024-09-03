<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product\Listing;
use Pimcore\Model\Asset\Folder;

#[AsCommand(
    name: 'app:clean',
    description: 'Fix tags for imported objects!'
)]
class CleanCommand extends AbstractCommand
{

    static int $level = 0;

    protected function configure()
    {
        $this
            ->addOption('list', null, InputOption::VALUE_NONE, 'If set, the task will list tagged objects, other options are ignored.')
            ->addOption('tag-only', null, InputOption::VALUE_NONE, 'If set, only new tags will be processed.')
            ->addOption('untag-only', null, InputOption::VALUE_NONE, 'If set, only existing tags will be processed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $homeFolder = Folder::getById(1);
        self::traverseAssetFolder($homeFolder);
        return Command::SUCCESS;
    }

    private static function traverseAssetFolder($assetFolder)
    {
        static::$level++;
        echo str_pad('', static::$level, ' ')."Found folder: " . $assetFolder->getFullPath() . "\n";
        if ($assetFolder instanceof Folder) {
            $childCount = 0;
            foreach ($assetFolder->getChildren() as $child) {
                $childCount++;
                if ($child instanceof Folder) {
                    self::traverseAssetFolder($child);
                }
            }
            if ($childCount === 0) {
                //$assetFolder->delete();
                echo str_pad('', static::$level, ' ')."Deleted folder: " . $assetFolder->getFullPath() . "\n";
            }
        }
        static::$level--;
    }
}
