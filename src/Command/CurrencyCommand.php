<?php

namespace App\Command;

use Carbon\Carbon;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Pimcore\Model\DataObject\Currency;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Folder;

#[AsCommand(
    name: 'app:currency',
    description: 'Retrieve Currency!'
)]
class CurrencyCommand extends AbstractCommand
{
    
    protected function configure()
    {
        $this
            ->addArgument('marketplace', InputOption::VALUE_OPTIONAL, 'The marketplace to import from.')
            ->addOption('download', null, InputOption::VALUE_NONE, 'If set, Shopify listing data will always be downloaded.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
/*        self::$downloadFlag = $input->getOption('download');
        self::$marketplaceArg = $input->getArgument('marketplace');*/

        $url = "https://www.tcmb.gov.tr/kurlar/today.xml";
        $xml = simplexml_load_file($url);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
	    echo "Current Date: ".date('m/d/Y')."\n";
        echo "TCMP Date: ".$array['@attributes']['Date']."\n";
        list($month, $day, $year) = explode('/', $array['@attributes']['Date']);
        $date = sprintf('%4d-%02d-%02d', $year, $month, $day);
        foreach ($array['Currency'] as $currency) {
            echo trim($currency['CurrencyName']) . " - " . $currency['ForexBuying']/$currency['Unit'];
            $currencyObject = Currency::getByCurrencyCode(trim($currency['CurrencyName']), ['limit' => 1,'unpublished' => true]);
            if (!$currencyObject) {
                echo " - Yeni";
                $currencyObject = new Currency();
                $currencyObject->setKey(trim($currency['CurrencyName']));
                $currencyObject->setCurrencyCode(trim($currency['CurrencyName']));
                $currencyObject->setParent(Folder::getByPath('/Ayarlar/Sabitler/Döviz-Kurları'));
            }
            $currencyObject->setDate(Carbon::createFromFormat('m/d/Y', $array['@attributes']['Date']));
            $currencyObject->setRate($currency['ForexBuying']/$currency['Unit']);
            $currencyObject->save();
            $this->updateCurrencyHistoryTable($currency,$date);
            echo "\n";
        }
        return Command::SUCCESS;
    }

    protected function updateCurrencyHistoryTable($currency,$date)
    {
        $currencyCode = $currency['@attributes']['CurrencyCode'];
        $value = number_format($currency['ForexBuying'] / $currency['Unit'],2);
        $db = \Pimcore\Db::get();
        $sql = "
            INSERT INTO iwa_currency_history (date, currency, value) 
            VALUES ('$date' , '$currencyCode', $value)
            ON DUPLICATE KEY UPDATE value = $value, date = '$date'
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }
}
