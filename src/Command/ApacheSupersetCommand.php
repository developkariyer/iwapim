<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\ScopingHttpClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\Product;
use App\Model\DataObject\VariantProduct;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Utils\Utility;


#[AsCommand(
    name: 'app:apache-superset',
    description: 'Superset API connection',
)]


class ApacheSupersetCommand extends AbstractCommand
{
   
    
    protected function configure() 
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->test();
        return Command::SUCCESS;
    }

    protected function test()
    {
        $httpClient = HttpClient::create();
        $link = "http://192.168.1.248:8088/api/v1/chart";
        $response = $httpClient->request('GET', $link,);
        $content = $response->getContent();
        echo $content;
    }
    
    


}