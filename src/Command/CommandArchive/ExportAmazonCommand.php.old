<?php
namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\AmazonVariant;
use Pimcore\Model\DataObject\AmazonVariant\Listing;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\EventListener\DataObjectListener;
use Pimcore\Model\DataObject\Folder;
 

#[AsCommand(
    name: 'app:exportAmazon',
    description: 'Export products!'
)]
class ExportAmazonCommand extends AbstractCommand
{
    private EventDispatcherInterface $eventDispatcher;
    private DataObjectListener $dataObjectListener;
    public static $csv = [];
    public static $valueTypes = [];

    public function __construct(EventDispatcherInterface $eventDispatcher, DataObjectListener $dataObjectListener)
    {
        parent::__construct();
        $this->eventDispatcher = $eventDispatcher;
        $this->dataObjectListener = $dataObjectListener;
    }

    private function removeListeners()
    {
        $this->eventDispatcher->removeSubscriber($this->dataObjectListener);
    }

    private function addListeners()
    {
        $this->eventDispatcher->addSubscriber($this->dataObjectListener);
    }

    protected function configure()
    {
        $this
            ->addArgument('marketplace', InputOption::VALUE_OPTIONAL, 'The marketplace to import from.')
            ->addOption('download', null, InputOption::VALUE_NONE, 'If set, Shopify listing data will always be downloaded.');
    }

    protected static function recursiveSearch($folder)
    {
        if ($folder instanceof Folder || $folder instanceof AmazonVariant) {
            foreach ($folder->getChildren() as $child) {
                static::recursiveSearch($child);
            }
            if ($folder instanceof AmazonVariant) {
                $summaries = json_decode($folder->getSummaries(), true);
                $summary = $summaries[0];
                $asin = $summary['asin'];
                $sku = $folder->getSku();
                $parent = $folder->getParent();
                $parentSku = ($parent instanceof AmazonVariant) ? $parent->getSku() : '';
                $json = json_decode($folder->getAttributes(), true);
                $eapi = $json['externally_assigned_product_identifier'] ?? [];
                $values = [];
                foreach ($eapi as $eap) {
                    if (!isset($values[$eap['type']])) {
                        $values[$eap['type']] = [];
                    }
                    $values[$eap['type']][] = $eap['value'];
                    static::$valueTypes[$eap['type']]=true;
                }
                static::$csv[] = [
                    $asin,
                    $values,
                    $parentSku,
                    $sku,
                ];
            }
        }
        return;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Handle the Ctrl+C (SIGINT) signal
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, function() use ($output) {
                $output->writeln("\nCtrl+C pressed. Cleaning up...");
                $this->addListeners();  // Ensure listeners are re-added on interrupt
                exit(Command::SUCCESS);  // Exit gracefully
            });
        }

        // Allow the signal to be handled
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
        }

        $this->removeListeners();

        // get objects in a folder, check if they are AmazonVariant of Folder. If not, continue

        static::recursiveSearch(Folder::getByPath('/'));

        $this->addListeners();

        echo "ASIN\t";
        foreach (static::$valueTypes as $type => $count) {
            echo "$type\t";
        }
        echo "Parent SKU\tSKU\n";

        foreach (static::$csv as $sku=>$row) {
            echo "$row[0]\t";
            foreach (static::$valueTypes as $type => $count) {
                if (isset($row[1][$type])) {
                    echo implode(',', $row[1][$type]);
                }
                echo "\t";
            }            
            echo "$row[2]\t";
            echo "$row[3]\n";
        }

        return Command::SUCCESS;
    }
}
