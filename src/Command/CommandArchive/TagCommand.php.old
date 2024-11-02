<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use \Pimcore\Model\Element\Tag;
use Pimcore\Model\DataObject\VariantProduct\Listing;

#[AsCommand(
    name: 'app:tag',
    description: 'Fix tags for imported objects!'
)]
class TagCommand extends AbstractCommand
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
        $tagListingObject = new Tag\Listing();
        $tagListingObject->setCondition('name = ?', 'Bağlanmamış');
        $bagliTag = $tagListingObject->current();
        if (!$bagliTag) {
            $bagliTag = new Tag();
            $bagliTag->setName('Bağlanmamış')->save();
        }
        echo "Loading objects...";
    
        $pageSize = 50;
        $offset = 0;
        $totalProcessed = 0;
    
        $tagged = $untagged = $tagAdded = $tagRemoved = 0;
    
        $listObject = new Listing();
        $listObject->setUnpublished(true);
        while (true) {
            $listObject->setLimit($pageSize);
            $listObject->setOffset($offset);
    
            $variants = $listObject->load();
            $totalProcessed += count($variants);
    
            if (empty($variants)) {
                break;
            }
    
            foreach ($variants as $variant) {
                $needTag = $variant->getMainProduct() ? false : true;
                if (!$needTag && $input->getOption('tag-only')) { continue; }
                if ($needTag && $input->getOption('untag-only')) { continue; }
                $tags = Tag::getTagsForElement('object', $variant->getId());
                if ($input->getOption('list') && !count($tags)) {
                    echo "    ".$variant->getId().": ".implode(', ', $tags)."\n";
                    continue;
                }
                foreach ($tags as $tag) {
                    if ($tag->getName() === 'Bağlanmamış') {
                        if ($needTag) {
                            $needTag = false;
                            $tagged++;
                            break;
                        } else {
                            Tag::removeTagFromElement('object', $variant->getId(), $tag);
                            $tagRemoved++;
                            break;
                        }
                    } else {
                        if ($needTag) {
                            Tag::addTagToElement('object', $variant->getId(), $bagliTag);
                            $tagAdded++;
                        } else {
                            $untagged++;
                        }        
                    }   
                }
            }
            echo " $totalProcessed";
    
            $offset += $pageSize;
        }
    
        echo "Finished\n";
        print_r([
            'totalProcessed' => $totalProcessed,
            'tagged' => $tagged,
            'untagged' => $untagged,
            'tagAdded' => $tagAdded,
            'tagRemoved' => $tagRemoved
        ]);
    
        return Command::SUCCESS;
    }
}
