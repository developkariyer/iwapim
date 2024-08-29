<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Product\Listing as ProductListing;
use Pimcore\Model\DataObject\ShopifyListing\Listing as ShopifyListingListing;
use Pimcore\Model\DataObject\ShopifyVariant\Listing as ShopifyVariantListing;
use Pimcore\Model\DataObject\AmazonVariant\Listing as AmazonVariantListing;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GuzzleHttp\Client;


class DefaultController extends FrontendController
{

    /**
     * @Route("/", name="default_homepage")
     */
    public function defaultAction(Request $request): Response
    {
        $productListing = new ProductListing();
        $productListing->setCondition('variationColor is NOT NULL');
        $publishedCount = $productListing->count();
        $productListing->setUnpublished(true);
        $unpublishedCount = $productListing->count();
        $productListing->setCondition('listingItems is NULL AND variationColor is NOT NULL');
        $productListingUnbound = $productListing->count();
        $productListing->setCondition('productDimension1 is NULL AND variationColor is NOT NULL');
        $dimensionProductCount = $productListing->count();
        $productListing->setCondition('packageDimension3 is NULL AND variationColor is NOT NULL');
        $dimensionPackageProductCount = $productListing->count();
        $productListing->setCondition('seoTitle is NULL AND variationColor is NOT NULL');
        $seoProductCount = $productListing->count();

        $shopifyListing = new ShopifyListingListing();
        $shopifyListing->setUnpublished(true);
        $shopifyListingCount = $shopifyListing->count();

        $shopifyVariant = new ShopifyVariantListing();
        $shopifyVariant->setUnpublished(true);
        $shopifyVariantCount = $shopifyVariant->count();

        $amazonListing = new AmazonVariantListing();
        $amazonListing->setUnpublished(true);
        $amazonListingCount = $amazonListing->count();

        return $this->render(
            'iwapim/index.html.twig', 
            [
                'published_product_count' => $publishedCount,
                'unpublished_product_count' => $unpublishedCount,
                'unbound_product_count' => $productListingUnbound,
                'dimension_product_count' => $dimensionProductCount,
                'dimension_package_product_count' => $dimensionPackageProductCount,
                'seo_product_count' => $seoProductCount,
                'shopify_listing_count' => $shopifyListingCount,
                'shopify_variant_count' => $shopifyVariantCount,
                'amazon_listing_count' => $amazonListingCount,
                'logged_in' => $request->cookies->get('id_token') ? true : false,
            ]
        );
    }

    /**
     * @Route("/login", name="default_login")
     */
    public function loginAction(Request $request): Response
    {
        if ($request->cookies->get('id_token')) {
            return $this->redirectToRoute('default_homepage');
        }
        $slackClientId = '';
        $slackSecret = '';

        // Debug session ID to check if it changes
        error_log("Session ID: " . session_id());

        $code = $request->query->get('code');
        $state = $request->query->get('state');
        $sessionState = $request->cookies->get('state');

        error_log("Code: $code   State: $state   Cookie State: $sessionState");

        if ($code && $state) {
            if ($state === $sessionState) {
                $url = 'https://slack.com/api/openid.connect.token';
                $data = [
                    'grant_type' => 'authorization_code',
                    'client_id' => $slackClientId,
                    'client_secret' => $slackSecret,
                    'code' => $code,
                    'redirect_uri' => 'https://mesa.iwa.web.tr/login',
                ];
                try {
                    $client = new Client();
                    $response = $client->post($url, ['form_params' => $data]);
                    $result = json_decode($response->getBody(), true);
                    error_log("Response: " . json_encode($result));
                    if (isset($result['id_token'])) {
                        $idToken = $result['id_token'];
                        setcookie("id_token", $idToken, time() + 36000, "/", ".iwa.web.tr", true, true);                        
                        return $this->redirectToRoute('default_homepage');
                    } else {
                        error_log('Failed to get ID token from Slack.');
                    }
                } catch (\Exception $e) {
                    error_log("Error during Slack authentication: " . $e->getMessage());
                    return new Response('Error during Slack authentication: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                error_log('Invalid state parameter.');
            }
        } else {
            if (!$sessionState) {
                error_log('Generating new state.');
                $sessionState = bin2hex(random_bytes(16));
                setcookie("state", $sessionState, time() + 3600, "/", ".iwa.web.tr", true, true);
            } else {
                error_log('State already exists.');
            }
            $nonce = bin2hex(random_bytes(16));
            setcookie("nonce", $nonce, time() + 3600, "/", ".iwa.web.tr", true, true);
            $redirectUri = "https://mesa.iwa.web.tr/login";
            return $this->render('iwapim/login.html.twig', [
                'slack_state' => $sessionState,
                'slack_nonce' => $nonce,
                'slack_redirect_uri' => urlencode($redirectUri),
                'slack_team_id' => 'T047M1SRFP0',
                'slack_client_id' => $slackClientId,
            ]);
        }
        return $this->redirectToRoute('default_login');
    }

    /**
     * @Route("/logout", name="default_logout")
     */
    public function logoutAction(Request $request): Response
    {
        setcookie("id_token", "", time() - 3600, "/", ".iwa.web.tr", true, true);
        return $this->redirectToRoute('default_login');
    }

    /**
     * @Route("/amazon", name="default_amazon")
     */
    public function amazonAction(Request $request): Response
    {   // Burak için
        $amazonListing = new AmazonVariantListing();
        $amazonListing->setUnpublished(true);
        $amazonListings = $amazonListing->load();
        $amazonlist = [];
        foreach ($amazonListings as $amazonListing) {
            $sku = $amazonListing->getSku();
            $summaries = json_decode($amazonListing->getSummaries(), true);
            $attributes = json_decode($amazonListing->getAttributes(), true);
            if ($summaries[0]['asin']==='B0B2F2V9F5') {
                error_log($amazonListing->getAttributes());
            }
            $amazonlist[] = [
                'sku' => $sku,
                'name' => $attributes['item_name'][0]['value'] ?? '',
                'asin' => $summaries[0]['asin'] ?? '',
                'fnsku' => $summaries[0]['fnsku'] ?? '',
                'eid' => $attributes['externally_assigned_product_identifier'][0]['value'] ?? '',
                'eidType' => $attributes['externally_assigned_product_identifier'][0]['type'] ?? '',
                'parent' => $attributes['child_parent_sku_relationship'][0]['parent_sku'] ?? '',
                'status' => (empty($attributes) || empty($summaries)) ? 'Not Synced' : 'OK',
            ];
        }
        return $this->render(
            'iwapim/amazon.html.twig', 
            [
                'amazon_listings' => $amazonlist,
            ]
        );
    }



}
