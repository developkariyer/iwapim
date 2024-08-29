<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MarketplaceController extends FrontendController
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
            $targetFile = $targetDir . basename($_FILES['file']['name'] ?? '');
            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
                    error_log('Failed to create directory');
                    return new Response('Failed to create directory', 500);
                }
            }
            if (isset($_FILES['file']['tmp_name']) && move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                $newFileName = "$targetDir$shopId.json";
                if (rename($targetFile, $newFileName)) {
                    error_log("File uploaded successfully");
                    return new Response('File uploaded successfully', 200);
                } else {
                    error_log('Failed to rename file');
                    return new Response('Failed to rename file', 500);
                }
            } else {
                error_log("Failed to upload file: {$_FILES['file']['error']}");
                error_log("File: {$_FILES['file']['tmp_name']}");
                return new Response('Failed to upload file', 500);
            }
        } else {
            error_log('Invalid request method');
            return new Response('Invalid request method', 405);
        }
    }


}
