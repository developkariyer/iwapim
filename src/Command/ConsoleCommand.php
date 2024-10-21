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
    name: 'app:console',
    description: 'Interactive PimCore Console',
)]
class ConsoleCommand extends AbstractCommand
{

    protected static function getJwtRemainingTime($jwt): int
    {
        $jwt = explode('.', $jwt);
        $jwt = json_decode(base64_decode($jwt[1]), true);
        return $jwt['exp'] - time();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ws = new Connector();
        $stores = $ws->storeSyncService;
        $categories = $ws->categorySyncService;
        $products = $ws->productSyncService;
        $listings = $ws->listingSyncService;
        $io = new SymfonyStyle($input, $output);
        $io->title('IWAPIM Interactive Shell');
        $context = [];

        while (true) {
            if ($ws instanceof Connector) {
                $storeStatus = $stores->status();
                $categoryStatus = $categories->status();
                $productStatus = $products->status();
                echo "Wisersell connected. Token expires in " . self::getJwtRemainingTime($ws->wisersellToken) . " seconds\n";
                echo "  Stores    :\tWisersell({$storeStatus['wisersell']})    \tPim({$storeStatus['pim']}) ({$storeStatus['expire']})\n";
                echo "  Categories:\tWisersell({$categoryStatus['wisersell']})    \tPim({$categoryStatus['pim']}) ({$categoryStatus['expire']})\n";
                echo "  Products  :\tWisersell({$productStatus['wisersell']})\tPim({$productStatus['pim']}) ({$productStatus['expire']})\n";
            }
            $command = $io->ask('');
            if (trim($command) === 'exit') {
                $io->success('Goodbye!');
                return 0;
            }
            try {
                ob_start();
                $result = eval($command . ';');
                $outputCaptured = ob_get_clean();
                if (!empty($outputCaptured)) {
                    $io->writeln($outputCaptured);
                }
                if ($result !== null) {
                    $io->writeln(var_export($result, true));
                }
                $context = get_defined_vars();
            } catch (\Throwable $e) {
                $outputCaptured = ob_get_clean();
                if (!empty($outputCaptured)) {
                    $io->writeln($outputCaptured);
                }
                $io->error($e->getMessage());
            }
        }
    }
}
