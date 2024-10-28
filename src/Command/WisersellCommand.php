<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Connector\Wisersell\Connector;

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
        $processCategories = $input->getOption('cagetories') | $processProducts;
        //actions
        $download = $input->getOption('download');
        $dump = $input->getOption('dump');
        $print = $input->getOption('print');
        $sync = $input->getOption('sync');

        $connector = new Connector();
        // download
        if ($processStores) {
            $connector->storeSyncService->load(true);
        }
        if ($processCategories) {
            $connector->categorySyncService->load(true);
        }
        if ($processProducts) {
            $connector->productSyncService->load(true);
        }
        if ($processListings) {
            $connector->listingSyncService->load(true);
        }

        // dump
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

        // print
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

        // sync
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

        return Command::SUCCESS;
    }

}
