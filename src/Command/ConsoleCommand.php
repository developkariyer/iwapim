<?php

namespace App\Command;

use App\Connector\Marketplace\ShopifyConnector;
use Carbon\Carbon;
use Doctrine\DBAL\Exception;
use Pimcore\Console\AbstractCommand;
use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Marketplace;
use Pimcore\Model\DataObject\VariantProduct;
use Pimcore\Model\DataObject\GroupProduct;
use App\Connector\Marketplace\Amazon\Connector as AmazonConnector;
use App\Connector\Marketplace\Amazon\Constants as AmazonConstants;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


#[AsCommand(
    name: 'app:console',
    description: 'Interactive Wisersell Connection Console',
)]
class ConsoleCommand extends AbstractCommand
{

    protected static function getJwtRemainingTime($jwt): int
    {
        $jwt = explode('.', $jwt);
        $jwt = json_decode(base64_decode($jwt[1]), true);
        return $jwt['exp'] - time();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RandomException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * @throws \Exception
     */
    public function setShopifySku(): void
    {   // $this->setShopifySku();

        $shopifyConnectors = [
            23978 => new ShopifyConnector(Marketplace::getById(23978)),
            84124 => new ShopifyConnector(Marketplace::getById(84124)),
            108415 => new ShopifyConnector(Marketplace::getById(108415)),
            108416 => new ShopifyConnector(Marketplace::getById(108416)),
            108417 => new ShopifyConnector(Marketplace::getById(108417)),
            108418 => new ShopifyConnector(Marketplace::getById(108418)),
            108419 => new ShopifyConnector(Marketplace::getById(108419)),
        ];

        $carbonYesterday = Carbon::now()->subDays(1);
        $variantObject = new VariantProduct\Listing();
        $pageSize = 5;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(false);
        $index = $offset;
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            $offset += $pageSize;
            foreach ($results as $listing) {
                $index++;
                echo "\rProcessing $index {$listing->getId()} ";
                if ($listing->getMarketplace()->getMarketplaceType() !== 'Shopify') {
                    continue;
                }
                if ($listing->getLastUpdate() < $carbonYesterday) {
                    continue;
                }
                $marketplaceId = $listing->getMarketplace()->getId();
                $mainProduct = $listing->getMainProduct();
                if (empty($mainProduct)) {
                    echo "No Main Product\n";
                    continue;
                }
                if (is_array($mainProduct)) {
                    $mainProduct = reset($mainProduct);
                }
                if (!$mainProduct instanceof Product) {
                    echo "Main Product is not an instance of Product\n";
                    continue;
                }
                $iwasku = $mainProduct->getIwasku();
                if (empty($iwasku)) {
                    echo "No Iwasku\n";
                    continue;
                }
                $connector = $shopifyConnectors[$marketplaceId] ?? null;
                if (empty($connector)) {
                    //echo "No Connector\n";
                    continue;
                }
                $connector->setSku($listing, $iwasku);
            }
        }
        echo "\nFinished\n";

    }

    /**
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function setShopifyBarcode(): void
    {   //  $this->setShopifyBarcode();
        $eans = [
            "180019" => "8684089422919",
            "180018" => "8684089422902",
            "180017" => "8684089422896",
            "180016" => "8684089422889",
            "180015" => "8684089422872",
            "180014" => "8684089422865",
            "180007" => "8684089422858",
            "180006" => "8684089422841",
            "180005" => "8684089422834",
            "180004" => "8684089422827",
            "180003" => "8684089422803",
            "180002" => "8684089422780",
            "180000" => "8684089422742",
            "179999" => "8684089422698",
            "179998" => "8684089422667",
            "179997" => "8684089422612",
            "179996" => "8684089422537",
            "179995" => "8684089422490",
            "179993" => "8684089422452",
            "179992" => "8684089422414",
            "179991" => "8684089422377",
            "179990" => "8684089422339",
            "179989" => "8684089422292",
            "179988" => "8684089422230",
            "179976" => "8684089422223",
            "179975" => "8684089422216",
            "179974" => "8684089422209",
            "179973" => "8684089422193",
            "179972" => "8684089422186",
            "179971" => "8684089422094",
            "179969" => "8684089422049",
            "179968" => "8684089422001",
            "179967" => "8684089421967",
            "179966" => "8684089421912",
            "179965" => "8684089421875",
            "179964" => "8684089421837",
            "179962" => "8684089421790",
            "179961" => "8684089421752",
            "179960" => "8684089421707",
            "179959" => "8684089420465",
            "179958" => "8684089420458",
            "179957" => "8684089420441",
            "179956" => "8684089420434",
            "179954" => "8684089420427",
            "179953" => "8684089420410",
            "179952" => "8684089420403",
            "179951" => "8684089420397",
            "179950" => "8684089420380",
            "179949" => "8684089420373",
            "179948" => "8684089420366",
            "179947" => "8684089420359",
            "179945" => "8684089420342",
            "179943" => "8684089420335",
            "179941" => "8684089420328",
            "179940" => "8684089420311",
            "179939" => "8684089420304",
            "179938" => "8684089420298",
            "179937" => "8684089420281",
            "179936" => "8684089420274",
            "179935" => "8684089420267",
            "179418" => "8684089425798",
            "179417" => "8684089425781",
            "179416" => "8684089425774",
            "179415" => "8684089425767",
            "179414" => "8684089425750",
            "179413" => "8684089425743",
            "179412" => "8684089425736",
            "179411" => "8684089425729",
            "179410" => "8684089425712",
            "179409" => "8684089425705",
            "179408" => "8684089425699",
            "179407" => "8684089425682",
            "179405" => "8684089425675",
            "179404" => "8684089425668",
            "179403" => "8684089425651",
            "179402" => "8684089425644",
            "179401" => "8684089425637",
            "179400" => "8684089425620",
            "179399" => "8684089425613",
            "179397" => "8684089425606",
            "179396" => "8684089425590",
            "179395" => "8684089425583",
            "179394" => "8684089425576",
            "179393" => "8684089425569",
            "179392" => "8684089425552",
            "179391" => "8684089425545",
            "179390" => "8684089425538",
            "179389" => "8684089425521",
            "179388" => "8684089425514",
            "179387" => "8684089425507",
            "179386" => "8684089425491",
            "179385" => "8684089425484",
            "179384" => "8684089425477",
            "179383" => "8684089425460",
            "179382" => "8684089425453",
            "179381" => "8684089425446",
            "179380" => "8684089425439",
            "179379" => "8684089425422",
            "179378" => "8684089425415",
            "179377" => "8684089425408",
            "179375" => "8684089425392",
            "179374" => "8684089425385",
            "179373" => "8684089425378",
            "179372" => "8684089425361",
            "179369" => "8684089425354",
            "179368" => "8684089425347",
            "179367" => "8684089425330",
            "179366" => "8684089425323",
            "179364" => "8684089425316",
            "179363" => "8684089425293",
            "179341" => "8684089425286",
            "179340" => "8684089425262",
            "179334" => "8684089425255",
            "179333" => "8684089425231",
            "179332" => "8684089425224",
            "179331" => "8684089425217",
            "179330" => "8684089425194",
            "179329" => "8684089425187",
            "179327" => "8684089425163",
            "179325" => "8684089425156",
            "179323" => "8684089425132",
            "179322" => "8684089425125",
            "179321" => "8684089425101",
            "179320" => "8684089425095",
            "179319" => "8684089425071",
            "179318" => "8684089425064",
            "179317" => "8684089425040",
            "179316" => "8684089425033",
            "179315" => "8684089425026",
            "179314" => "8684089425002",
            "179313" => "8684089424999",
            "179312" => "8684089424975",
            "179311" => "8684089424968",
            "179310" => "8684089424951",
            "179308" => "8684089424937",
            "179307" => "8684089424920",
            "179306" => "8684089424913",
            "179305" => "8684089424890",
            "179304" => "8684089424883",
            "179303" => "8684089424852",
            "179301" => "8684089424845",
            "179300" => "8684089424821",
            "179299" => "8684089424807",
            "179298" => "8684089424784",
            "179297" => "8684089424777",
            "179296" => "8684089424760",
            "179295" => "8684089424746",
            "179294" => "8684089424739",
            "179287" => "8684089424715",
            "179286" => "8684089424708",
            "179285" => "8684089424692",
            "179284" => "8684089424685",
            "179283" => "8684089424678",
            "179282" => "8684089424661",
            "179281" => "8684089424654",
            "179280" => "8684089424647",
            "179278" => "8684089424630",
            "179277" => "8684089424623",
            "179276" => "8684089424616",
            "179275" => "8684089424609",
            "179274" => "8684089424593",
            "179273" => "8684089424586",
            "179272" => "8684089424579",
            "179271" => "8684089424555",
            "179270" => "8684089424548",
            "179269" => "8684089424524",
            "179268" => "8684089424517",
            "179267" => "8684089424500",
            "179266" => "8684089424487",
            "179265" => "8684089424470",
            "179264" => "8684089424456",
            "179263" => "8684089424449",
            "179241" => "8684089424425",
            "179240" => "8684089424401",
            "179239" => "8684089424388",
            "179238" => "8684089424371",
            "179237" => "8684089424357",
            "179236" => "8684089424333",
            "179227" => "8684089423978",
            "179226" => "8684089423961",
            "179225" => "8684089423954",
            "179224" => "8684089423923",
            "179223" => "8684089423916",
            "179222" => "8684089423893",
            "179221" => "8684089423879",
            "179220" => "8684089423862",
            "179219" => "8684089423848",
            "179218" => "8684089423831",
            "179217" => "8684089423817",
            "179216" => "8684089423800",
            "179215" => "8684089423794",
            "179214" => "8684089423770",
            "179213" => "8684089423763",
            "179212" => "8684089423749",
            "179210" => "8684089423732",
            "179206" => "8684089423718",
            "179202" => "8684089423701",
            "179198" => "8684089423695",
            "179191" => "8684089423671",
            "179190" => "8684089423664",
            "179189" => "8684089423657",
            "179188" => "8684089423640",
            "179187" => "8684089423633",
            "179186" => "8684089423626",
            "179179" => "8684089423619",
            "179178" => "8684089423602",
            "179177" => "8684089423596",
            "179176" => "8684089423589",
            "179175" => "8684089423572",
            "179174" => "8684089423565",
            "179166" => "8684089423558",
            "179165" => "8684089423534",
            "179164" => "8684089423527",
            "179163" => "8684089423510",
            "179162" => "8684089423497",
            "179161" => "8684089423480",
            "179160" => "8684089423466",
            "179159" => "8684089423459",
            "179158" => "8684089423442",
            "179157" => "8684089423435",
            "179156" => "8684089423411",
            "179155" => "8684089423404",
            "179143" => "8684089423381",
            "179139" => "8684089423374",
            "179138" => "8684089423350",
            "179136" => "8684089423343",
            "179135" => "8684089423336",
            "179133" => "8684089423312",
            "179132" => "8684089423305",
            "179130" => "8684089423299",
            "179129" => "8684089423282",
            "179127" => "8684089423275",
            "179126" => "8684089423251",
            "179124" => "8684089423244",
            "179123" => "8684089423237",
            "179121" => "8684089423213",
            "179102" => "8684089422810",
            "179101" => "8684089422797",
            "179100" => "8684089422773",
            "179099" => "8684089422766",
            "179098" => "8684089422759",
            "179097" => "8684089422735",
            "179096" => "8684089422728",
            "179095" => "8684089422711",
            "179094" => "8684089422704",
            "179093" => "8684089422681",
            "179092" => "8684089422674",
            "179091" => "8684089422650",
            "179090" => "8684089422643",
            "179089" => "8684089422636",
            "179088" => "8684089422629",
            "179087" => "8684089422605",
            "179085" => "8684089422599",
            "179084" => "8684089422582",
            "179083" => "8684089422575",
            "179082" => "8684089422568",
            "179080" => "8684089422551",
            "179068" => "8684089422544",
            "179067" => "8684089422520",
            "179066" => "8684089422513",
            "179065" => "8684089422506",
            "179064" => "8684089422483",
            "179063" => "8684089422476",
            "179062" => "8684089422469",
            "179061" => "8684089422445",
            "179044" => "8684089422438",
            "179043" => "8684089422421",
            "179042" => "8684089422407",
            "179041" => "8684089422391",
            "179040" => "8684089422384",
            "179039" => "8684089422360",
            "179038" => "8684089422353",
            "179037" => "8684089422346",
            "179035" => "8684089422322",
            "179034" => "8684089422315",
            "179033" => "8684089422308",
            "179032" => "8684089422285",
            "179031" => "8684089422278",
            "179030" => "8684089422261",
            "179029" => "8684089422254",
            "179028" => "8684089422247",
            "179015" => "8684089422087",
            "179014" => "8684089422070",
            "179013" => "8684089422063",
            "179011" => "8684089422056",
            "179010" => "8684089422032",
            "179009" => "8684089422025",
            "179008" => "8684089422018",
            "179007" => "8684089421998",
            "179006" => "8684089421981",
            "179004" => "8684089421974",
            "179003" => "8684089421950",
            "179002" => "8684089421943",
            "179001" => "8684089421936",
            "178989" => "8684089421929",
            "178988" => "8684089421905",
            "178987" => "8684089421899",
            "178986" => "8684089421882",
            "178985" => "8684089421868",
            "178984" => "8684089421851",
            "178983" => "8684089421844",
            "178982" => "8684089421820",
            "178981" => "8684089421813",
            "178980" => "8684089421806",
            "178979" => "8684089421783",
            "178978" => "8684089421776",
            "178976" => "8684089421769",
            "178975" => "8684089421745",
            "178972" => "8684089421738",
            "178971" => "8684089421721",
            "178968" => "8684089421714",
            "178957" => "8684089421691",
            "178956" => "8684089421684",
            "178955" => "8684089421677",
            "178954" => "8684089421660",
            "178953" => "8684089421653",
            "178952" => "8684089421646",
            "178951" => "8684089421622",
            "178950" => "8684089421615",
            "178939" => "8684089421592",
            "178938" => "8684089421585",
            "178937" => "8684089421561",
            "178936" => "8684089421554",
            "178934" => "8684089421547",
            "178933" => "8684089421523",
            "178932" => "8684089421516",
            "178931" => "8684089421493",
            "178929" => "8684089421486",
            "178928" => "8684089421462",
            "178927" => "8684089421455",
            "178926" => "8684089421431",
            "178925" => "8684089421424",
            "178924" => "8684089421417",
            "178923" => "8684089421400",
            "178922" => "8684089421394",
            "178920" => "8684089421387",
            "178919" => "8684089421370",
            "178918" => "8684089421363",
            "178917" => "8684089421356",
            "178916" => "8684089421349",
            "178915" => "8684089421332",
            "178914" => "8684089421325",
            "178913" => "8684089421318",
            "178912" => "8684089421301",
            "178900" => "8684089421295",
            "178897" => "8684089421288",
            "178894" => "8684089421271",
            "178890" => "8684089421264",
            "178889" => "8684089421257",
            "178888" => "8684089421240",
            "178886" => "8684089421233",
            "178883" => "8684089421226",
            "178882" => "8684089421219",
            "178881" => "8684089421202",
            "178880" => "8684089421196",
            "178879" => "8684089421189",
            "178878" => "8684089421172",
            "178873" => "8684089421165",
            "178872" => "8684089421158",
            "178871" => "8684089421141",
            "178870" => "8684089421134",
            "178868" => "8684089421127",
            "178867" => "8684089421110",
            "178866" => "8684089421097",
            "178865" => "8684089421080",
            "178864" => "8684089421073",
            "178863" => "8684089421066",
            "178862" => "8684089421059",
            "178861" => "8684089421042",
            "178860" => "8684089421035",
            "178854" => "8684089421028",
            "178853" => "8684089421011",
            "178852" => "8684089421004",
            "178851" => "8684089420991",
            "178850" => "8684089420984",
            "178849" => "8684089420977",
            "178847" => "8684089420960",
            "178846" => "8684089420953",
            "178845" => "8684089420946",
            "178844" => "8684089420939",
            "178842" => "8684089420922",
            "178841" => "8684089420915",
            "178840" => "8684089420908",
            "178839" => "8684089420892",
            "178837" => "8684089420885",
            "178836" => "8684089420878",
            "178835" => "8684089420861",
            "178828" => "8684089420854",
            "178826" => "8684089420847",
            "178825" => "8684089420830",
            "178824" => "8684089420823",
            "178823" => "8684089420816",
            "178821" => "8684089420809",
            "178819" => "8684089420793",
            "178818" => "8684089420786",
            "178817" => "8684089420779",
            "178816" => "8684089420762",
            "178809" => "8684089420755",
            "178801" => "8684089420748",
            "178799" => "8684089420731",
            "178798" => "8684089420724",
            "178797" => "8684089420717",
            "178791" => "8684089420700",
            "178789" => "8684089420694",
            "178788" => "8684089420687",
            "178783" => "8684089420670",
            "178781" => "8684089420663",
            "178779" => "8684089420656",
            "178777" => "8684089420649",
            "178775" => "8684089420632",
            "178773" => "8684089420625",
            "178771" => "8684089420618",
            "178769" => "8684089420601",
            "178765" => "8684089420595",
            "178764" => "8684089420588",
            "178763" => "8684089420571",
            "178762" => "8684089420564",
            "178761" => "8684089420557",
            "178760" => "8684089420540",
            "178758" => "8684089420533",
            "178757" => "8684089420526",
            "178756" => "8684089420519",
            "178755" => "8684089420502",
            "178754" => "8684089420496",
            "178753" => "8684089420489",
            "178752" => "8684089420472",
            "177114" => "8684089425934",
            "177113" => "8684089425811",
            "177111" => "8684089425804",
            "177110" => "8684089425309",
            "177109" => "8684089425279",
            "177108" => "8684089425248",
            "177107" => "8684089425200",
            "177106" => "8684089425170",
            "177104" => "8684089425149",
            "177103" => "8684089425118",
            "177102" => "8684089425088",
            "177101" => "8684089425057",
            "177100" => "8684089425019",
            "177099" => "8684089424982",
            "177098" => "8684089424944",
            "177097" => "8684089424906",
            "177096" => "8684089424869",
            "177095" => "8684089424814",
            "177093" => "8684089424722",
            "177092" => "8684089424562",
            "177091" => "8684089424531",
            "177090" => "8684089424494",
            "177089" => "8684089424463",
            "177088" => "8684089424432",
            "177086" => "8684089424395",
            "177085" => "8684089424340",
            "177084" => "8684089424319",
            "177083" => "8684089424296",
            "177082" => "8684089424203",
            "177081" => "8684089424173",
            "177079" => "8684089424166",
            "177078" => "8684089424142",
            "177077" => "8684089424128",
            "177076" => "8684089424111",
            "177075" => "8684089424104",
            "177074" => "8684089424098",
            "177072" => "8684089424043",
            "177071" => "8684089424029",
            "177070" => "8684089424012",
            "177069" => "8684089423992",
            "177068" => "8684089423947",
            "177067" => "8684089423909",
            "177065" => "8684089423855",
            "177064" => "8684089423824",
            "177063" => "8684089423787",
            "177062" => "8684089423756",
            "177061" => "8684089423725",
            "177060" => "8684089423688",
            "177059" => "8684089423541",
            "177058" => "8684089423503",
            "177057" => "8684089423473",
            "177056" => "8684089423428",
            "177055" => "8684089423398",
            "177054" => "8684089423367",
            "177053" => "8684089423329",
            "177052" => "8684089423206",
            "177050" => "8684089423190",
            "177049" => "8684089423183",
            "177048" => "8684089423176",
            "177047" => "8684089423169",
            "177046" => "8684089423152",
            "177045" => "8684089423145",
            "177044" => "8684089423121",
            "177043" => "8684089423107",
            "177042" => "8684089423077",
            "177041" => "8684089423053",
            "177039" => "8684089423039",
            "177038" => "8684089423008",
            "177037" => "8684089422964",
            "177036" => "8684089422957",
            "177035" => "8684089422940",
            "177034" => "8684089422933",
            "177033" => "8684089422926",
            "175584" => "8684089424838",
            "175583" => "8684089424791",
            "175582" => "8684089424753",
            "175563" => "8684089424418",
            "175562" => "8684089424364",
            "175561" => "8684089424326",
            "175560" => "8684089424302",
            "175559" => "8684089424289",
            "175558" => "8684089424272",
            "175549" => "8684089424265",
            "175548" => "8684089424258",
            "175546" => "8684089424241",
            "175545" => "8684089424234",
            "175544" => "8684089424227",
            "175543" => "8684089424210",
            "175542" => "8684089424197",
            "175541" => "8684089424180",
            "175539" => "8684089424159",
            "175538" => "8684089424135",
            "175537" => "8684089424081",
            "175536" => "8684089424074",
            "175535" => "8684089424067",
            "175534" => "8684089424050",
            "175532" => "8684089424036",
            "175531" => "8684089424005",
            "175530" => "8684089423985",
            "175529" => "8684089423930",
            "175528" => "8684089423886",
            "175527" => "8684089423268",
            "175525" => "8684089423220",
            "175524" => "8684089423138",
            "175523" => "8684089423114",
            "175522" => "8684089423091",
            "175521" => "8684089423084",
            "175520" => "8684089423060",
            "175518" => "8684089423046",
            "175517" => "8684089423022",
            "175516" => "8684089423015",
            "175515" => "8684089422995",
            "175514" => "8684089422988",
            "175513" => "8684089422971",
            "175511" => "8684089421639",
            "175510" => "8684089421608",
            "175509" => "8684089421578",
            "175508" => "8684089421530",
            "175507" => "8684089421509",
            "175506" => "8684089421479",
            "175505" => "8684089421448",
            "175504" => "8684089421103",
        ];

        $connector = new ShopifyConnector(Marketplace::getById(23978));
        $carbon7daysAgo = Carbon::now()->subDays(7);
        foreach ($eans as $id => $ean) {
            echo "$id $ean\n";
            $variantProduct = VariantProduct::getById($id);
            if (!$variantProduct || $variantProduct->getLastUpdate() < $carbon7daysAgo) {
                echo "VariantProduct $id not found or too old\n";
                continue;
            }
            $connector->setBarcode($variantProduct, $ean);
        }
    }

    public function connectAmazonUs()
    {   // $this->connectAmazonUs();
        $grp = GroupProduct::getById(267975);
        $grpArray = [];
        $prdListObj = new Product\Listing();
        $prdListObj->setUnpublished(false);
        $prdList = $prdListObj->load();
        foreach ($prdList as $prd) {
            $found = false;
            echo "{$prd->getId()} ";
            $listings = $prd->getListingItems();
            foreach ($listings as $listing) {
                echo "{$listing->getId()} ";
                $amazonMarketplaces = $listing->getAmazonMarketplace() ?? [];
                foreach ($amazonMarketplaces as $amazonMarketplace) {
                    echo "{$amazonMarketplace->getMarketplaceId()} ";
                    if ($amazonMarketplace->getStatus() === 'Active' && $amazonMarketplace->getMarketplaceId() === 'US') {
                        $grpArray[] = $prd;
                        echo "added";
                        $found = true;
                        break;
                    }
                }
                if ($found) {
                    break;
                }
            }
            echo "\n";
        }
        $grp->setProducts($grpArray);
        $grp->save();
    }

    public function deleteFr()
    {   // $this->deleteFr();
        $db = \Pimcore\Db::get();
        $amazonConnector = new AmazonConnector(Marketplace::getById(200568)); // UK Amazon
        $amazonEuMarkets = ['IT', 'FR']; //['DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'SE', 'PL'];
        $euMarketsPlaceholder = implode("','", $amazonEuMarkets);
        $query = "
            SELECT marketplaceId, sku
            FROM object_collection_AmazonMarketplace_varyantproduct
            WHERE fieldname = 'amazonMarketplace' 
                AND marketplaceId IN ('$euMarketsPlaceholder')
            ORDER BY marketplaceId, sku
        ";
        $skulist = $db->fetchAllAssociative($query);
        $total = count($skulist);
        $index = 0;
        foreach ($skulist as $sku) {
            $index++;
            echo "\rProcessing $index/$total ";
            $country = $sku['marketplaceId'];
            if (!in_array($country, $amazonEuMarkets)) {
                continue;
            }
            $sku = $sku['sku'];
            echo " $country $sku ";
            try {
                $amazonConnector->utilsHelper->patchGPSR($sku, $country);
            } catch (\Exception $e) {
                echo "Error: ".$e->getMessage()."\n";
            }
        }
        echo "\nFinished\n";
    }

    public function addMatteToVariantColors()
    {   // $this->addMatteToVariantColors();
        $pageSize = 5;
        $offset = 0;
        $listingObject = new Product\Listing();
        $listingObject->setUnpublished(true);
        $listingObject->setLimit($pageSize);
        $index = $offset;
        while (true) {
            $listingObject->setOffset($offset);
            $products = $listingObject->load();
            if (empty($products)) {
                break;
            }
            $offset += $pageSize;
            foreach ($products as $product) {
                $index++;
                echo "\rProcessing $index {$product->getId()} ";
                if ($product->level() < 1 || $product->getInheritedField('productCategory') !== 'IWA Metal') {
                    continue;
                }
                $variationColor = $product->getVariationColor();
                if (in_array($variationColor, ['Gold', 'Copper', 'Silver', 'IGOB', 'IGOS', 'ISOB', 'ISOG'])) {
                    echo "{$product->getInheritedField('productCategory')} {$variationColor} ";
                    $product->setVariationColor("Matte {$variationColor}");
                    $product->save();
                    echo "saved\n";
                    file_put_contents(PIMCORE_PROJECT_ROOT . "/tmp/product_add_matte.log", json_encode([
                        'timestamp' => date('Y-m-d H:i:s'),
                        'product' => $product->getId(),
                        'productCategory' => $product->getInheritedField('productCategory'),
                        'oldVariationColor' => $variationColor,
                        'newVariationColor' => "Matte {$variationColor}"
                    ]) . "\n", FILE_APPEND);
                }
            }
        }
    }

    public function getAmazonInfo()
    {   // $this->getAmazonInfo();
        
        $amazonConnector = [
            [
                'connector' => new AmazonConnector(Marketplace::getById(200568)), // UK Amazon
                'countries' => ['UK', 'DE', 'FR', 'IT', 'ES', 'NL', 'TR', 'SE', 'PL'],
            ],
            [
                'connector' => new AmazonConnector(Marketplace::getById(149795)), // US Amazon
                'countries' => ['US', 'MX'],
            ],
            [
                'connector' => new AmazonConnector(Marketplace::getById(234692)), // CA Amazon
                'countries' => ['CA'],
            ]
        ];
        $variantObject = new VariantProduct\Listing();
        $pageSize = 5;
        $offset = 0;
        $variantObject->setLimit($pageSize);
        $variantObject->setUnpublished(false);
        $index = $offset;
        while (true) {
            $variantObject->setOffset($offset);
            $results = $variantObject->load();
            if (empty($results)) {
                break;
            }
            $offset += $pageSize;
            foreach ($results as $listing) {
                $index++;
                echo "\rProcessing $index {$listing->getId()}";
                $amazonMarketplaces = $listing->getAmazonMarketplace() ?? [];
                if (empty($amazonMarketplaces)) {
                    continue;
                }
                echo "\n";
                foreach ($amazonMarketplaces as $amazonMarketplace) {
                    $country = $amazonMarketplace->getMarketplaceId();
                    foreach ($amazonConnector as $connector) {
                        if (!in_array($country, $connector['countries'])) {
                            continue;
                        }
                        $sku = $amazonMarketplace->getSku();
                        if (empty($sku)) {
                            continue;
                        }
                        echo " $country:$sku";
                        $connector['connector']->utilsHelper->getInfo($sku, $country);
                    }
                }
                echo "\n";
            }
        }
        echo "\nFinished\n";
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //$amazon = new AmazonConnector(Marketplace::getById(149795)); // US: 149795  UK: 200568
        //$amazon->patchCustom(sku: "CA_41_Burgundy_XL", country: "US", attribute: "item_type_keyword", operation: "delete", value: "");

        $io = new SymfonyStyle($input, $output);
        $io->title('IWAPIM Interactive Shell');
        $context = [];

        while (true) {
            $command = $io->ask('');
            if (trim($command) === 'exit') {
                $io->success('Goodbye!');
                return 0;
            }
            try {
                $result = eval($command . ';');
                if ($result !== null) {
                    $io->writeln(var_export($result, true));
                }
                echo "\n";
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
