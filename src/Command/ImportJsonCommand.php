<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Folder;

#[AsCommand(
    name: 'app:import-json',
    description: 'Imports JSON from phpmyadmin JSON export!'
)]
class ImportJsonCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->addArgument('json', InputArgument::OPTIONAL, 'The json file to import.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jsonArg = $input->getArgument('json');
        if (empty($jsonArg)) {
            $output->writeln('Please provide a json file to import.');
            return Command::FAILURE;
        }
        
        $jsonData = json_decode(file_get_contents($jsonArg), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln('Invalid JSON file.');
            return Command::FAILURE;
        }

        foreach ($jsonData as $data) {
            if (isset($data['type']) && $data['type'] === 'table' && isset($data['data'])) {
                foreach ($data['data'] as $productData) {
                    $this->importProduct($productData);
                }
            }
        }

        return Command::SUCCESS;
    }

    private function importProduct(array $data): void
    {
        $rootFolder = '/Ürünler'; // Root folder for all products

        // Ensure root folder exists
        $root = Folder::getByPath($rootFolder);
        if (!$root) {
            $root = new Folder();
            $root->setKey('Ürünler');
            $root->setParentId(1); // Assuming 1 is the root folder ID
            $root->save();
        }

        // Get or create category folder
        $categoryFolder = $rootFolder . '/' . \Pimcore\File::getValidFilename($data['category']);
        $parent = Folder::getByPath($categoryFolder);
        if (!$parent) {
            $parent = new Folder();
            $parent->setKey(\Pimcore\File::getValidFilename($data['category']));
            $parent->setParent($root);
            $parent->save();
        }

        // Check if product already exists by name
        $product = Product::findByField('name', $data['name']);
        if (!$product) {
            $product = new Product();
            $product->setKey(\Pimcore\File::getValidFilename($data['name']));
            $product->setParent($parent);
        }

        // Set the product fields
        $product->setName($data['name']);
        $product->setProductCategory($data['category']);
        $product->setPackageDimension1($data['dimension1']);
        $product->setPackageDimension2($data['dimension2']);
        $product->setPackageDimension3($data['dimension3']);
        $product->setPackageWeight($data['weight']);

        // Set the Fieldcollection for product identifiers
        $fieldCollection = new Fieldcollection();
        if (!empty($data['fnsku'])) {
            $identifier = new Fieldcollection\Data\Identifiers();
            $identifier->setIdentifierType('fnsku');
            $identifier->setIdentifierValue($data['fnsku']);
            $fieldCollection->add($identifier);
        }

        $product->setProductIdentifiers($fieldCollection);

        // Save the product
        $product->save();
    }
}

/*


    private static function importFromExcel()
    {
        $rawData = <<<RAWDATA

IAS-1	Damla Allah ve Muhammed (sav) Lafzı Set	20x30cm 30x45cm 50x76cm
IAS-2	Kufi Kare Felak Nas Set	40x40cm 50x50cm 60x60cm
IAS-3	Kufi Kare Ayetel Kürsi, Felak, Nas Set	40x40cm 50x50cm 60x60cm
IAS-4	Mescid El Aksa, Mescid El Haram, Mescid El Nebevi Set	30cm 40cm 50cm
IAS-5	Haram-Aksa Set	30cm 40cm 50cm
IAS-6	Haram-Nebevi Set	30cm 40cm 50cm
IAS-7	Aksa-Nebevi Set	30cm 40cm 50cm
IAS-8	Kare Arkası Siyah Sülüs Subhanallah Elhamdulillah Set	30x30cm 50x50cm
IAS-9	Kare Arkası Siyah Sülüs Subhanallah AllahuEkber Set	30x30cm 50x50cm
IAS-10	Kare Arkası Siyah Sülüs Elhamdulillah AllahuEkber Set	30x30cm 50x50cm
IAS-11	Kare Arkası Siyah Sülüs Subhanallah, Elhamdulillah, Alla	30x30cm 50x50cm
IAS-12	Klasik Ayetel Kursi, Felak Çubuklu, Nas Çubuklu Set	30x39cm 40x52cm 50x65cm
IAS-13	Klasik Ayetel Kursi, Felak Çubuklu, Nas Çubuklu, İhlas Büyük Lafız Set	30x39cm 40x52cm 50x65cm
IAS-14	Klasik Felak Çubuklu, Nas Çubuklu Set	30x34.2cm 40x45.6cm 50x57cm
IAS-15	Klasik Tek Parça Subhanallah, Elhamdulillah Allahuekber Set	30x30cm 50x50cm
IAS-16	Klasik Tek Parça Subhanallah, Elhamdulillah Set	30x30cm 50x50cm
IAS-17	Klasik Tek Parça Subhanallah, AllahuEkber Set	30x30cm 50x50cm
IAS-18	Klasik Tek Parça Elhamdulillah, Allahuekber Set	30x30cm 50x50cm
IAS-19	Kare Arkası Boş Subhanallah, Elhamdulillah, Allahuekber Set	30x30cm 40x40cm 50x50cm 60x60cm
IAS-20	Kare Arkası Boş Maşallah, Estağfirillah, Hasbiyallah Set	30x30cm 40x40cm 50x50cm 60x60cm
IAS-21	Paternli Yuvarlak Subhanallah, Elhamdulillah, Allahuekber Set	30x30cm 40x40cm 50x50cm 60x60cm
IAS-22	Paternli Yuvarlak Allah ve Muhammed (sav) Lafızları Set	30x30cm 40x40cm 50x50cm 60x60cm
IAS-23	Kufi Kare Yamuk Subhanallah, Elhamdulillah, Allahuekber. Set	30x30cm 50x50cm
IAS-24	Klasik Sülüs Allah ve Muhammed (sav) Lafızları Set	30x30cm 45x45cm
IAS-25	Büyük Kul Felak-Nas Set	70x45cm
IAS-26	Büyük Kul-Kafirun-İhlas Set	70x45cm
IAS-27	Büyük Kul Felak-Nas İhlas Set	70x45cm
IAS-28	Büyük Kul Felak-Nas-Kafirun-İhlas Set 	70x45cm
IAS-29	3 Parça Felak ve Nas Set	50x57cm 60x70cm 90x105cm
IAS-30	Gül Desenli Allah ve Muhammed Lafızları MDF Tabon Üzeri Akrilik İkilii SET	20x20cm  30x30cm 40x40cm 50x50cm 60x60cm
IAS-31	Tek Parça 2 Renkli AK, Çubuklu Felak, Çubuklu Nas Set	30x39cm 40x52cm 50x65cm
IAS-32	Tek Parça 2 Renkli Çubuklu Felak, Çubuklu Nas Set	30x34.2cm 40x45.6cm 50x57cm
IAS-33	Sabır, Şükür, Dua, Tevekkül 4Lü Ahşap Set	30x30cm 40x40cm 50x50cm 60x60cm
IAS-34	Dikdörtgen Beyaz Arkaplan Ayetel Kürsi, Felak, Nas Üçlü (3) SET	30x40cm 40x53cm 50x66cm
IAS-35	Dikdörtgen Siyah Arkaplan Ayetel Kürsi, Felak, Nas Üçlü (3) SET	30x40cm 40x53cm 50x66cm
IAS-36	Shukran Ramadan Mubarak ve Eid Mubarak Kandil SET	18x31cm
IAS-37	Yuvarlak Mescid-i Haram, Mescid-i Nebevi, Mescid-i Aksa Üçlü SET	30x30cm 40x40cm 50x50cm 60x60cm
IAS-38	Diwani Uzun Allah ve Muhammed Lafzı	17.5x30cm 23x40cm 29x50cm 35x60cm
IAS-39	Diwani Allah ve Muhammed Lafızları ile Yuvarlak Tevhid SET	30cm(Boy)  40cm(Boy) 50cm(Boy) 60cm(Boy)  70cm(Boy)
IAS-40	Diwani Allah ve Muhammed Lafızları ile Divani Besmele SET	30cm(Boy)  40cm(Boy) 50cm(Boy) 60cm(Boy)  70cm(Boy)
IAS-41	Gül Desenli Çevre Süslemeli SubhanAllah, Elhamdulillah, Allahuekber Üçlü SET	20x20cm  30x30cm 40x40cm 50x50cm 60x60cm
IAS-42	Gül Desenli Çevre Süslemeli Allah ve Muhammed Lafizlari İkili SET	20x20cm  30x30cm 40x40cm 50x50cm 60x60cm
IAS-43	Kufi SubhanAllah, Elhamdulillah, AllahuEkber V2	20x20cm  30x30cm 40x40cm 50x50cm 60x60cm
IAS-44	Start with End With İki Parça MDF Üzeri Akrilik iki Parça SET	30x11cm 40x14cm 50x18cm 60x21cm
IAS-45	Kare Arkası Boş Latin-Arabic Subhanallah, Elhamdulillah, Allahuekber Set	30x30cm 40x40cm 50x50cm 60x60cm
IAS-46	Allah (cc)  Muhammed (sav) ve Ali (ra) Lafzı Damla Set	20x30cm 30x45cm 50x76cm
IAS-47	YA Allah (cc) YA  Muhammed (sav) ve YA Ali (ra) Lafız Set	30cm 45cm  60cm 75cm
IAS-48	Ahşap Diamond Ayetel Kursi, Felak, Nas, İhlas Set	49x49 (her biri)
IAS-49	7'li Zikir Set, Hem Arapça Hem Latin	30x11cm 40x14cm 50x18cm 60x21cm
IAS-50	7'li Zikir Set, Sadece Latin	30x11cm 40x14cm 50x18cm 60x21cm
IAS-51	Kiswa Ya Hayyu Ya Qayyum, Ya Rahman Ya Raheem Set	35x50cm
IAS-52	Ehli Beyt 6'lı Ya Lafız Set	Medium: 30cm) Large: 45cm X-Large: 60cm XX-Large: 70cm
IAS-53	Kare Köşe Bordürlü Allah (cc) Muhammed (sav)	30x30cm 40x40cm 50x50cm 60x60cm
IAS-54	Yuvarlak Kenar Bordürlü Allah (cc) Muhammed (sav)	30x30cm 40x40cm 50x50cm 60x60cm
IAS-55	Mihrab Version 3 (Ara Bölmesiz) Set	Standart
IAS-56	Hülefa-i Raşidin ve Allah (cc) Muhammed (sav) 6'lı Dikey Geometrik Üzeri Set	20x30cm 27x40cm 33x50cm 40x60cm 50x75cm
IAS-57	Ehl-i Beyt ve Allah (cc) Muhammed (sav) 6'lı Dikey Geometrik Üzeri Set	20x30cm 27x40cm 33x50cm 40x60cm 50x75cm


RAWDATA;

        $lines = explode("\n", $rawData);
        $mainFolder = Utility::checkSetPath("Ürünler");
        $productFolder = Utility::checkSetPath("IA Islamic Ahşap", $mainFolder);
        echo "\n";
        foreach ($lines as $line) {
            $line = trim($line);
            $line = trim($line, "\n");
            if (empty($line)) {
                continue;
            }
            $parts = explode("\t", $line);
            if (count($parts) !== 3) {
                continue;
            }
            echo "Processing: $line ";
            $rawCode = $parts[0];
            $name = $parts[1];
            $sizes = $parts[2];
            if (preg_match('/^([A-Z]{2,3}-)(\d+)([A-Z]?)$/', $rawCode, $matches)) {
                $paddedNumber = str_pad($matches[2], 3, '0', STR_PAD_LEFT);
                $code = $matches[1] . $paddedNumber . $matches[3];
            } else {
                echo "invalid code\n";
                continue;
            }
            $test = Product::findByField('productIdentifier', $code);
            if (!$test) {
                echo "Not exists ";
                $test = new Product();
                $test->setProductIdentifier($code);
                $test->setName(trim($name));
                $test->setParent($productFolder);
                $test->setPublished(false);
                $test->setKey(trim("$code $name"));
                $test->save();
            }
            if (is_array($test)) {
                $test = $test[0];
            }
            if (!$test instanceof Product) {
                echo "Not a product\n";
                continue;
            }
            if ($test->level()>0) {
                echo "Not a main product\n";
                continue;
            }
            $sizes = explode(' ', $sizes);
            foreach ($sizes as $size) {
                $size = trim($size);
                if (empty($size)) {
                    continue;
                }
                $size = str_replace(' ', '', $size);
                $sizeTest = explode("\n", $test->getVariationSizeList());
                if (in_array($size, $sizeTest)) {
                    echo "Size $size exists\n";
                }
                $sizeProduct = new Product();
                $sizeProduct->setParent($test);
                $sizeProduct->setKey($size);
                $sizeProduct->setVariationSize($size);
                $sizeProduct->setPublished(false);
                try {
                    $sizeProduct->save();
                } catch (\Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
            echo "OK\n";
        }
    }

        private static function existingProduct($key)
    {
        $list = new ProductListing();
        $list->setCondition("`key` = ?", [$key]);
        $list->setUnpublished(true);
        $list->setLimit(1);
        return $list->current();
    }

    private static function collateSkuList()
    {
        self::$itemCodes = [];
        $list = new ProductListing();
        $products = $list->load();
        foreach ($products as $product) {
            if ($product->getLevel() !== Product::SIZE_VARIANT) {
                continue;
            }
            $variants = $product->getListingItems();
            foreach ($variants as $variant) {
                if ($variant instanceof ShopifyVariant && !empty($variant->getSku())) {
                    self::$skuList[$variant->getSku()] = $product;
                }
            }
        }
    }

    private static function existingSku($sku)
    {
        die("TODO");
        self::getSkuList();
        return self::$skuList[$sku] ?? null;
    }


    private static function saveProduct($listing, $parent)
    {
        if (self::$dryRunFlag) {
            return true;
        }
        $product = new Product();
        $product->setParent($parent);
        $product->setKey(substr($listing->getHandle(), 0, 190));
        $product->setName($listing->getTitle());
        $product->setDescription($listing->getBodyHtml());
        $product->setNameEnglish($listing->getTitle());
        $product->setProductCode($product->generateUniqueCode(5));
        $product->setProductIdentifier('XX999'); // means need to be updated
        $product->setIwaskuActive(false);
        $product->setPublished(true);
        return $product->save() ? $product : null;
    }

    private static function addVariantToProduct($product, $variantProduct)
    {
        if (!$product instanceof Product || !$variantProduct instanceof ShopifyVariant) {
            echo "    Invalid product or variant.\n";
            return false;
        }
        if (self::$dryRunFlag) {
            echo "    Dry Run Mode\n";
            return true;
        }
        try {
            $variants = $product->getListingItems();
            $variantIds = array_map(function($variant) {
                return $variant->getId();
            }, $variants);
            if (!in_array($variantProduct->getId(), $variantIds)) {
                $variants[] = $variantProduct;
                $product->setListingItems($variants);
                if ($product->save() && !empty($variantProduct->getSku())) {
                    self::$skuList[$variantProduct->getSku()] = $product;
                    echo "    Added variant #{$variantProduct->getId()} with SKU {$variantProduct->getSku()} to Product #{$product->getId()}.\n";
                }
            }
        } catch (\Exception $e) {
            error_log('Error updating product variants: ' . $e->getMessage());
        }     
    }


    private static function checkOptionsValidity($optionsJson)
    {
        $options = json_decode($optionsJson, true);
        if (count($options) > 2) {
            return [true, [], [], 0, 0];
        }
        $sizes = $colors = [];
        $colorIndex = $sizeIndex = 0;
        $broken = false;
        foreach ($options as $index=>$option) {
            if (in_array($option['name'], ['Size', 'Boyut', 'Ebat', 'Ebat/Size', 'Size/Ebat', 'Boyut/Size', 'Size/Boyut', 'Ebat/Boyut', 'Boyut/Ebat', 'Zincir Boyu', 'Chain Size', 'Chain Lenght'])) {
                $sizes = $option['values'];
                $sizeIndex = $index+1;
            } elseif (in_array($option['name'], ['Color', 'Renk', 'Renk/Color', 'Color/Renk'])) {
                $colors = $option['values'];
                $colorIndex = $index+1;
            } else {
                $broken = true;
                break;
            }
        }
        if (empty($sizes) && empty($colors)) {
            $broken = true;
        }
        if (empty($colors)) {
            $colors = ['Renk Yok'];
        }
        if (empty($sizes)) {
            $sizes = ['Ebat Yok'];
        }
        return [$broken, $sizes, $colors, $sizeIndex, $colorIndex];
    }

    private static function getProducts()
    {
        $list = new ProductListing();
        $products = $list->load();
        return $products;
    }

    private static function generateProductsFromShopify($marketplace)
    {
        $importedFolder = self::checkSetPath("Imported");
        $marketplaceFolder = self::checkSetPath(static::sanitizeVariable($marketplace->getKey()), $importedFolder);
        $urunler = self::checkSetPath('Ürünler');
        echo "Processing Shopify Marketplace {$marketplace->getKey()} ...\n";
        foreach (self::getShopifyListings(marketplaceFolder:$marketplaceFolder) as $index=>$listing) {
            echo "    ($index) Processing Product {$listing->getTitle()} ...\n";
            [$broken, $sizes, $colors, $sizeIndex, $colorIndex] = self::checkOptionsValidity($listing->getOptionsJson());
            if (!$broken) {
                $sizeOption = "getOption{$sizeIndex}";
                $colorOption = "getOption{$colorIndex}";
                $categoryFolder = static::sanitizeVariable($listing->getProductType());
                if (empty($categoryFolder)) {
                    $categoryFolder = 'Tasnif-Edilmemiş';
                }
                $category = self::checkSetPath($categoryFolder, $urunler);
                $shopifyVariants = $listing->getChildren();
                $variants = [];
                $colorVariants = [];
                foreach ($shopifyVariants as $shopifyVariant) {
                    if (!empty($shopifyVariant->getSku()) && in_array($shopifyVariant->getSku(), array_keys(self::$skuList))) {
                        $oldProduct = self::$skuList[$shopifyVariant->getSku()];
                        echo "    Found a matching SKU for {$shopifyVariant->getSku()} in ShopifyVariant #{$shopifyVariant->getId()} with Product #{$oldProduct->getId()}.\n";
                        self::addVariantToProduct($oldProduct, $shopifyVariant);
                        continue;
                    }
                    $variantColor = $colorIndex ? self::sanitizeVariable($shopifyVariant->$colorOption()) : 'Renk Yok';
                    $variantSize = $sizeIndex ? self::sanitizeVariable($shopifyVariant->$sizeOption()) : 'Ebat Yok';
                    if (!isset($variants[$variantColor][$variantSize])) {
                        if (!isset($colorVariants[$variantColor])) {
                            if (!$product = self::existingProduct(substr($listing->getHandle(), 0, 190))) {
                                $product = self::saveProduct(listing:$listing, parent:$category);
                            }            
                            $colorVariants[$variantColor] = $product->checkColorSize($variantColor);
                            if (!$colorVariants[$variantColor]) {
                                $colorVariants[$variantColor] = $product->addColor($variantColor);
                            }
                        }
                        $variants[$variantColor][$variantSize] = $colorVariants[$variantColor]->checkColorSize($variantSize);
                        if (!$variants[$variantColor][$variantSize]) {
                            $variants[$variantColor][$variantSize] = $colorVariants[$variantColor]->addSize($variantSize);
                        }
                    }
                    self::addVariantToProduct($variants[$variantColor][$variantSize], $shopifyVariant);
                }
            }
        }
    }

    private static function matchShopifyVariant($variant)
    {
        if ($product = self::existingSku($variant->getSku())) {
            if ($product instanceof Product) {
                if (!self::$dryRunFlag) {
                    self::addVariantToProduct($product, $variant);
                }
                echo "    Matched variant {$variant->getTitle()} with SKU {$variant->getSku()} to {$product->getKey()}.\n";
                return $product;
            }
        }
        return null;
    }

    private static function matchShopifyVariants($marketplace) {
        $importedFolder = self::checkSetPath("Imported");
        $marketplaceFolder = self::checkSetPath(static::sanitizeVariable($marketplace->getKey()), $importedFolder);
        foreach (self::getShopifyVariants(marketplaceFolder:$marketplaceFolder) as $index=>$variant) {
            self::matchShopifyVariant($variant);
        }
    }

    
    private function setSC()
    {
        $list = new Product\Listing();
        $list->setCondition("`productIdentifier` LIKE ?", ['SC%']);
        $list->setUnpublished(true);
        $products = $list->load();
        foreach ($products as $product) {
            if ($product->level() != 1) {
                continue;
            }
            echo "Product: {$product->getKey()} ({$product->getId()})";
            $size = $product->getVariationSize();
            if (empty($size)) {
                echo " No size.\n";
                continue;
            }
            if (preg_match('/(\d+(\.\d+)?)x(\d+(\.\d+)?)cm/', $size, $matches)) {
                $width = $matches[1];
                $height = $matches[3];
            } else {
                echo " Invalid size.\n";
                continue;
            }
            echo " Size: $size ($width x $height)";
            $product->setProductDimension1($width);
            $product->setProductDimension2($height);
            switch ($size) {
                case '35x35cm':
                    $product->setPackageDimension1(42);
                    $product->setPackageDimension2(42);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(2.4);
                    break;
                case '80x33cm':
                case '33x80cm':
                    $product->setPackageDimension1(88);
                    $product->setPackageDimension2(42);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(5.5);
                    break;
                case '90x33cm':
                case '33x90cm':
                    $product->setPackageDimension1(97);
                    $product->setPackageDimension2(42);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(5.7);
                    break;
                case '110x50cm':
                case '50x110cm':
                    $product->setPackageDimension1(117);
                    $product->setPackageDimension2(58);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(11.7);
                    break;
                case '50x50cm':
                    $product->setPackageDimension1(57);
                    $product->setPackageDimension2(57);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(4.8);
                    break;
                case '61x61cm':
                    $product->setPackageDimension1(68);
                    $product->setPackageDimension2(68);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(6.2);
                    break;
                case '65x65cm':
                    $product->setPackageDimension1(70);
                    $product->setPackageDimension2(70);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(7.4);
                    break;
                case '50x35cm':
                case '35x50cm':
                    $product->setPackageDimension1(56);
                    $product->setPackageDimension2(42);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(3.3);
                    break;
                case '61x46cm':
                case '46x61cm':
                    $product->setPackageDimension1(67);
                    $product->setPackageDimension2(53);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(5);
                    break;
                case '70x55cm':
                case '55x70cm':
                    $product->setPackageDimension1(77);
                    $product->setPackageDimension2(63);
                    $product->setPackageDimension3(5);
                    $product->setPackageWeight(7.5);
                    break;
                default:
                    echo " No match.";
            }
            $product->save();
            echo " OK.\n";
        }
    }

    
    private function setSize()
    {
        $list = new Product\Listing();
        $list->setUnpublished(true);
        $products = $list->load();
        foreach ($products as $product) {
            if ($product->level() != 1) {
                continue;
            }
            if ($product->getProductDimension1() && $product->getProductDimension2() && strlen($product->getKey())>10 ) {
                continue;
            }
            echo "Product: {$product->getKey()} ({$product->getId()})";
            $size = $product->getVariationSize();
            if (empty($size)) {
                echo " No size.\n";
                continue;
            }
            if (preg_match('/(\d+(\.\d+)?)x(\d+(\.\d+)?)cm/', $size, $matches)) {
                $width = $matches[1];
                $height = $matches[3];
                echo " Size: $size ($width x $height)";
                $product->setProductDimension1($width);
                $product->setProductDimension2($height);
            } else {
                echo " Invalid size.\n";
            }
            try {
                $product->save();
            } catch (\Exception $e) {
                echo $e->getMessage();
                sleep(1);
                $product->delete();
                echo " deleted";
            }
            echo " OK.\n";
        }
    }

    private function deleteColors()
    {
        $productList = new Product\Listing();
        $productList->setUnpublished(true);
        $productList->setCondition("variationColor = ? OR variationColor = ? OR variationSize = ? OR variationSize = ?", ['Akrilik&Siyah', 'Akrilik Karma', 'Akrilik&Siyah', 'Akrilik Karma']);
        $products = $productList->load();
        foreach ($products as $product) {
            if ($product->level()>0) {
                echo "Product: {$product->getKey()} ({$product->getId()})";
                $product->delete();
                echo "OK\n";
            }
        }
        echo "Finished.\n";
    }


                if ($input->getOption('debugger')) {
                echo "Custom Debug Output.\n";
                $list = new Listing();
                $list->setUnpublished(true);
                $products = $list->load();
                foreach ($products as $product) {
                    //add shopify variants to product objects
                    foreach ($product->getChildren() as $child) {
                        if ($child instanceof ShopifyVariant) {
                            $product->addVariant($child);
                            echo "Product: {$product->getKey()} ({$product->getId()}) + Child: {$child->getId()}\n";
                            $mainListing = $child->getMainListing();
                            if (is_array($mainListing) && !empty($mainListing)) {
                                $mainListing = $mainListing[0];                                
                            }
                            if ($mainListing instanceof ShopifyListing) {
                                echo "    Main Product: {$mainListing->getId()}\n";
                                $child->setParent($mainListing);
                                $child->save();
                            }
                        }
                    }*/
                    
                    /* //reset all listing items in Product objects
                    if (!empty($product->getListingItems())) {
                        echo "Product: {$product->getKey()} ({$product->getId()})\n";
                        $product->setListingItems([]);
                        $product->save();
                    }
                    */
                    /* //check product codes less than 5 characters 
                    if (strlen($product->getProductCode())!==5) {
                        echo "Product: {$product->getKey()} ({$product->getId()})\n";
                        $product->setProductCode($product->generateUniqueCode(5));
                        $product->save();
                    }*/
                    /* //reset all iwasku fields 
                    if ($product->getIwaskuActive() || strlen($product->getIwasku())) {
                        echo "Product: {$product->getKey()} ({$product->getId()})\n";
                        $product->setIwaskuActive(null);
                        $product->setIwasku(null);
                        $product->save();                    
                    }
                }
            }


                        if ($input->getOption('delete-colors')) {
                echo "Deleting wrong colors: ";
                self::deleteColors();
                return Command::SUCCESS;

            }

            if ($input->getOption('import-excel')) {
                echo "Importing Excel File: ";
                self::importFromExcel();
                return Command::SUCCESS;
            }

            if ($input->getOption('set-sc')) {
                echo "Setting SC: ";
                self::setSC();
                return Command::SUCCESS;
            }
    
            if ($input->getOption('set-size')) {
                echo "Setting Sizes: ";
                self::setSize();
                return Command::SUCCESS;
            }
    



*/