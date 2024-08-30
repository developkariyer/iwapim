<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product\Listing;

#[AsCommand(
    name: 'app:clean',
    description: 'Fix tags for imported objects!'
)]
class CleanCommand extends AbstractCommand
{
    
    protected function configure()
    {
        $this
            ->addOption('list', null, InputOption::VALUE_NONE, 'If set, the task will list tagged objects, other options are ignored.')
            ->addOption('tag-only', null, InputOption::VALUE_NONE, 'If set, only new tags will be processed.')
            ->addOption('untag-only', null, InputOption::VALUE_NONE, 'If set, only existing tags will be processed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pageSize = 200;
        $offset = 0;

        $listObject = new Listing();
        $listObject->setUnpublished(true);
        while (true) {
            $listObject->setLimit($pageSize);
            $listObject->setOffset($offset);
            $products = $listObject->load();
            if (empty($products)) {
                break;
            }
            foreach ($products as $product) {
                if ($product->level() == 1 && count($product->getChildren())) {
                    $parent = $product->getParent();
                    echo "* Found second level product {$product->getKey()}\n";
                    $failed = false;
                    foreach ($product->getChildren() as $child) {
                        $child->setParent($parent);
                        $child->setVariationSize($product->getVariationSize());
                        if ($child->save()) {
                            echo "  + Moved child {$child->getKey()} to parent {$parent->getKey()}\n";
                        } else {
                            echo "  - Failed to move child {$child->getKey()} to parent {$parent->getKey()}\n";
                            $failed = true;
                        }
                    }
                    if (!$failed) {
                        $product->delete();
                        echo "  + Deleted product {$product->getKey()}\n";
                    } else {
                        echo "  - Failed to delete product {$product->getKey()}\n";
                    }
                }
            }
            $output->writeln('Processed ' . ($offset + count($products)) . ' objects');
            $offset += $pageSize;
        }
        return Command::SUCCESS;
    }
}
