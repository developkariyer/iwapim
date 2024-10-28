<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Connector\Wisersell\Connector;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:wisersell',
    description: 'Wisersell API connection and sync',
)]

class WisersellCommand extends AbstractCommand
{

    protected function configure() 
    {
        $this
            ->addOption('stores', null, InputOption::VALUE_NONE, 'Process stores (default=log to wisersell_stores.json) (WARNING: Sync cancelled. For syncing, manual action required in PIM)')
            ->addOption('categories', null, InputOption::VALUE_NONE, 'Process categories (default=log to wisersell_categories.json)')
            ->addOption('products', null, InputOption::VALUE_NONE, 'Process products (default=log to wisersell_products.json). Also processes categories.')
            ->addOption('listings', null, InputOption::VALUE_NONE, 'Process listings (default=log to wisersell_listings.json). Also processes stores. NOT RECOMMENDED ALONE!')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Process all (default=log to relevant json file)')
            ->addOption('download', null, InputOption::VALUE_NONE, 'Force download from Wisersell')
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Dump Wisersell data to relevant json file. Requires stores, categories, products, listings or all.')
            ->addOption('print', null, InputOption::VALUE_NONE, 'Output Wisersell data to screen. Requires stores, categories, products, listings or all.')
            ->addOption('sync', null, InputOption::VALUE_NONE, 'Sync PIM and Wisersell. Requires stores, categories, products, listings or all.')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // objects
        $processAll = $input->getOption('all');
        $processListings = $input->getOption('listings') | $processAll;
        $processProducts = $input->getOption('products') | $processAll;
        $processStores = $input->getOption('stores') | $processListings;
        $processCategories = $input->getOption('categories') | $processProducts;
        //actions
        $sync = $input->getOption('sync');
        $print = $input->getOption('print');
        $dump = $input->getOption('dump');
        $download = $input->getOption('download');

        $output->writeln('Wisersell API connection and sync');
        $output->writeln('----------------------------------');
        $output->writeln('Stores: ' . ($processStores ? 'Yes' : 'No'));
        $output->writeln('Categories: ' . ($processCategories ? 'Yes' : 'No'));
        $output->writeln('Products: ' . ($processProducts ? 'Yes' : 'No'));
        $output->writeln('Listings: ' . ($processListings ? 'Yes' : 'No'));
        $output->writeln('Sync: ' . ($sync ? 'Yes' : 'No'));
        $output->writeln('Print: ' . ($print ? 'Yes' : 'No'));
        $output->writeln('Dump: ' . ($dump ? 'Yes' : 'No'));
        $output->writeln('Download: ' . ($download ? 'Yes' : 'No'));
        $output->writeln('----------------------------------');
        
        $connector = new Connector();
        // download
        if ($download) {
            if (!$processStores && !$processCategories && !$processProducts && !$processListings) {
                $output->writeln('Download requires stores, categories, products, listings or all.');
                return Command::FAILURE;
            }
            if ($processStores) {
                $connector->storeSyncService->download();
            }
            if ($processCategories) {
                $connector->categorySyncService->download();
            }
            if ($processProducts) {
                $connector->productSyncService->download();
            }
            if ($processListings) {
                $connector->listingSyncService->download();
            }
        }

        // dump
        if ($dump) {
            if (!$processStores && !$processCategories && !$processProducts && !$processListings) {
                $output->writeln('Dump requires stores, categories, products, listings or all.');
                return Command::FAILURE;
            }
            if ($processStores) {
                $connector->storeSyncService->dump();
            }
            if ($processCategories) {
                $connector->categorySyncService->dump();
            }
            if ($processProducts) {
                $connector->productSyncService->dump();
            }
            if ($processListings) {
                $connector->listingSyncService->dump();
            }
        }

        // print
        if ($print) {
            if (!$processStores && !$processCategories && !$processProducts && !$processListings) {
                $output->writeln('Print requires stores, categories, products, listings or all.');
                return Command::FAILURE;
            }
            if ($processStores) {
                print_r($connector->storeSyncService->wisersellStores);
            }
            if ($processCategories) {
                print_r($connector->categorySyncService->wisersellCategories);
            }
            if ($processProducts) {
                print_r($connector->productSyncService->wisersellProducts);
            }
            if ($processListings) {
                print_r($connector->listingSyncService->wisersellListings);
            }
        }

        // sync
        if ($sync) {
            if (!$processStores && !$processCategories && !$processProducts && !$processListings) {
                $output->writeln('Sync requires stores, categories, products, listings or all.');
                return Command::FAILURE;
            }
            if ($processStores) {
                $connector->storeSyncService->sync();
            }
            if ($processCategories) {
                $connector->categorySyncService->sync();
            }
            if ($processProducts) {
                $connector->productSyncService->sync();
            }
            if ($processListings) {
                $connector->listingSyncService->sync();
            }
        }

        return Command::SUCCESS;
    }

}
