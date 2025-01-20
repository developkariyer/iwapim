<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtsyController extends FrontendController
{

    /**
     * @Route("/marketplace/etsy", name="upload_etsy")
     * 
     * This action aims to get Etsy API information from remote API script.
     * It is run locally on Etsy Workstation and uploads retrieved data to
     * this server through this function
     */
    public function etsyAction(Request $request): Response
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("File upload request received from remote Etsy IP: {$_SERVER['REMOTE_ADDR']}");
            $shopId = $_POST['shop_id'] ?? null;
            if (!$shopId) {
                error_log("Missing shop_id");
                return new Response('Missing shop_id', 400);
            }
            error_log("Shop ID: $shopId");
            $targetDir = PIMCORE_PROJECT_ROOT . '/tmp/';
            if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
                error_log('Failed to create directory');
                return new Response('Failed to create directory', 500);
            }
            if (!isset($_FILES['file']['tmp_name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
                error_log("Failed to upload file: {$_FILES['file']['error']}");
                return new Response('Failed to upload file', 500);
            }
            $uploadedFile = $_FILES['file']['tmp_name'];
            $originalFileName = $_FILES['file']['name'] ?? '';
            $newFileName = "$targetDir$shopId.json";
            if (pathinfo($originalFileName, PATHINFO_EXTENSION) === 'gz') {
                $gzFileContents = file_get_contents($uploadedFile);
                if ($gzFileContents === false) {
                    error_log('Failed to read gzipped file');
                    return new Response('Failed to read gzipped file', 500);
                }
                $jsonData = gzdecode($gzFileContents);
                if ($jsonData === false) {
                    error_log('Failed to decompress gzipped file');
                    return new Response('Failed to decompress gzipped file', 500);
                }
                if (file_put_contents($newFileName, $jsonData) === false) {
                    error_log('Failed to save decompressed JSON file');
                    return new Response('Failed to save decompressed JSON file', 500);
                }
                error_log("Gzipped file uploaded and decompressed successfully");
            } else {
                if (!move_uploaded_file($uploadedFile, $newFileName)) {
                    error_log('Failed to move uploaded file');
                    return new Response('Failed to move uploaded file', 500);
                }
                error_log("Plain JSON file uploaded successfully");
            }    
            return new Response('File uploaded successfully', 200);
        } else {
            error_log('Invalid request method');
            return new Response('Invalid request method', 405);
        }
    }
    

}
