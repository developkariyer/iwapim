<?php

namespace App\Command;

use FilesystemIterator;
use Pimcore\Model\Asset;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\Element\DuplicateFullPathException;
use RecursiveDirectoryIterator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:import-assets',
    description: 'Imports all files from public/var/assets to Pimcore assets'
)]
class ImportAssetsCommand extends AbstractCommand
{
    /**
     * @throws DuplicateFullPathException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceDir = 'public/var/assets';
        $this->importAssets($sourceDir);

        $this->writeInfo('Assets import completed successfully.');
        return Command::SUCCESS;
    }

    /**
     * @throws DuplicateFullPathException
     */
    private function importAssets(string $sourceDir): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relativePath = str_replace($sourceDir, '', $file->getPathname());

            // Skip files in _default_upload_bucket
            if (str_contains($relativePath, '_default_upload_bucket')) {
                continue;
            }

            $targetPath = '/' . $relativePath;
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
                // Check if the asset already exists
                $asset = Asset::getByPath($targetPath);
                if ($asset) {
                    // Check if the existing asset's size matches the source file's size
                    if ($asset->getFileSize() == $file->getSize()) {
                        $this->writeComment("Skipping existing asset: $targetPath");
                        continue;
                    }
                }

                // Add file as an asset
                $asset = new Asset();
                $asset->setFilename(basename($targetPath));
                $asset->setParent(Asset::getByPath(dirname($targetPath)));
                $asset->setStream(fopen($file->getPathname(), 'r'));
                $asset->save();
            }
        }
    }
}
