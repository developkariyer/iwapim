<?php

namespace App\Command;

use Pimcore\Model\Asset;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import-assets',
    description: 'Imports all files from public/var/assets to Pimcore assets'
)]
class ImportAssetsCommand extends AbstractCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceDir = 'public/var/assets';
        $this->importAssets($sourceDir, '/');

        $this->writeInfo('Assets import completed successfully.');
        return Command::SUCCESS;
    }

    private function importAssets(string $sourceDir, string $targetDir)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relativePath = str_replace($sourceDir, '', $file->getPathname());
            
            // Skip files in _default_upload_bucket
            if (strpos($relativePath, '_default_upload_bucket') !== false) {
                continue;
            }
            
            $targetPath = $targetDir . $relativePath;
            $targetPath = str_replace('\\', '/', $targetPath); // Normalize the path for different OS

            if ($file->isDir()) {
                // Ensure target directory exists
                $assetFolder = Asset::getByPath($targetPath);
                if (!$assetFolder) {
                    $assetFolder = new Asset\Folder();
                    $assetFolder->setFilename(basename($targetPath));
                    $assetFolder->setParent(Asset::getByPath(dirname($targetPath)));
                    $assetFolder->save();
                }
            } else {
                // Add file as an asset
                $asset = Asset::getByPath($targetPath);
                if (!$asset) {
                    $asset = new Asset();
                    $asset->setFilename(basename($targetPath));
                    $asset->setParent(Asset::getByPath(dirname($targetPath)));
                    $asset->setData(file_get_contents($file->getPathname()));
                    $asset->save();
                }
            }
        }
    }
}
