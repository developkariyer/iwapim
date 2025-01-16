<?php

namespace App\Controller;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Pimcore\Controller\FrontendController;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;


class DefaultController extends FrontendController
{

    /**
     * @Route("/", name="default_homepage")
     */
    public function defaultAction(): Response
    {
/*        $productListing = new ProductListing();
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
*/
        return $this->render(
            'base.html.twig'
        );
    }

    /**
     * @Route("/login", name="default_login")
     * @throws RandomException|GuzzleException
     */
    public function loginAction(Request $request): Response
    {
        if ($request->cookies->get('id_token')) {
            return $this->redirectToRoute('default_homepage');
        }
        $slackClientId = $_ENV['SLACK_CLIENT_ID'] ?? '';
        $slackSecret = $_ENV['SLACK_CLIENT_SECRET'] ??'';

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
                } catch (Exception $e) {
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
    public function logoutAction(): Response
    {
        setcookie("id_token", "", time() - 3600, "/", ".iwa.web.tr", true, true);
        return $this->redirectToRoute('default_login');
    }

}
