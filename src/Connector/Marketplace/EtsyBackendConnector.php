<?php


/*
This code runs in remote Etsy workstations only. Put here for reference.

<?php

function generateCodeVerifier($length = 128) {
    return bin2hex(random_bytes($length / 2));
}

function generateCodeChallenge($codeVerifier) {
    return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
}

function initOauth($shopId) {
    $etsyData = file_exists("etsy_{$shopId}.json") ? json_decode(file_get_contents("etsy_{$shopId}.json"), true) : [];

    $clientId = readline("Enter Client ID (" . ($etsyData['client_id'] ?? 'none') . "): ");
    $clientSecret = readline("Enter Client Secret (" . ($etsyData['client_secret'] ?? 'none') . "): ");
    $clientId = !empty($clientId) ? $clientId : ($etsyData['client_id'] ?? '');
    $clientSecret = !empty($clientSecret) ? $clientSecret : ($etsyData['client_secret'] ?? '');
    $etsyData['client_id'] = $clientId;
    $etsyData['client_secret'] = $clientSecret;
    $codeVerifier = generateCodeVerifier();
    $etsyData['code_verifier'] = $codeVerifier;
    $codeChallenge = generateCodeChallenge($codeVerifier);
    $redirectUri = readline("Enter Redirect URI (" . ($etsyData['redirect_uri'] ?? 'none') . "): ");
    $redirectUri = !empty($redirectUri) ? $redirectUri : ($etsyData['redirect_uri'] ?? '');
    $etsyData['redirect_uri'] = $redirectUri;

    file_put_contents("etsy_{$shopId}.json", json_encode($etsyData, JSON_PRETTY_PRINT));

    // Generate the authorization URL
    $scope = urlencode('transactions_r listings_r shops_r');
    $state = bin2hex(random_bytes(16)); // Generate a random state for CSRF protection
    $authUrl = "https://www.etsy.com/oauth/connect?response_type=code&client_id={$clientId}&redirect_uri=" . urlencode($redirectUri) . "&scope={$scope}&code_challenge={$codeChallenge}&code_challenge_method=S256&state={$state}";

    // Output the authorization URL and ask for the authorization code
    echo "State: $state\n";
    echo "Visit the following URL to authorize the app:\n\n$authUrl\n\n";

    // Ask the user to input the full redirect URL after authorization
    $authResponse = readline("Enter the full redirect URL after authorization (begins with {$etsyData['redirect_uri']}): ");

    // Extract the authorization code and state from the response URL
    $urlParts = parse_url($authResponse);
    parse_str($urlParts['query'], $queryParams);

    $authCode = $queryParams['code'] ?? null;
    $returnedState = $queryParams['state'] ?? null;

    if ($authCode && $returnedState === $state) {
        echo "Authorization successful. Code: $authCode\n";

        // Store the authorization code in the JSON file
        $etsyData['authorization_code'] = $authCode;
        file_put_contents("etsy_{$shopId}.json", json_encode($etsyData, JSON_PRETTY_PRINT));

        echo "Authorization code saved successfully.\n";
    } else {
        echo "Authorization failed or state mismatch.\n";
        exit;
    }
}

function init($shopId) {
    $etsyData = json_decode(file_get_contents("etsy_{$shopId}.json"), true);
    if (empty($etsyData['authorization_code'])) {
        initOauth($shopId);
    }
    // Check if the access token is expired or not set
    if (empty($etsyData['access_token']) || time() > ($etsyData['created_at'] ?? 0) + ($etsyData['expires_in'] ?? 0)) {
        if (empty($etsyData['access_token'])) {
            // Exchange the authorization code for an access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.etsy.com/v3/public/oauth/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'authorization_code',
                'client_id' => $etsyData['client_id'],
                'client_secret' => $etsyData['client_secret'],
                'code' => $etsyData['authorization_code'],
                'redirect_uri' => $etsyData['redirect_uri'],
                'code_verifier' => $etsyData['code_verifier']
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "x-api-key: {$etsyData['client_id']}"
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            if ($httpCode === 200) {
                $data = json_decode($response, true);
                $etsyData['access_token'] = $data['access_token'];
                $etsyData['refresh_token'] = $data['refresh_token'];
                $etsyData['expires_in'] = $data['expires_in'];
                $etsyData['created_at'] = time();
                echo "Access token acquired.\n";
            } else {
                echo "Error acquiring token: $httpCode\n";
                echo "Response: $response\n";
                echo "cURL error: $curlError\n";
                exit;
            }
            curl_close($ch);
        } else {
            // Refresh the access token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.etsy.com/v3/public/oauth/token");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'refresh_token',
                'client_id' => $etsyData['client_id'],
                'client_secret' => $etsyData['client_secret'],
                'refresh_token' => $etsyData['refresh_token']
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "x-api-key: {$etsyData['client_id']}"
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            if ($httpCode === 200) {
                $data = json_decode($response, true);
                $etsyData['access_token'] = $data['access_token'];
                $etsyData['refresh_token'] = $data['refresh_token'];
                $etsyData['expires_in'] = $data['expires_in'];
                $etsyData['created_at'] = time();
                echo "Access token refreshed.\n";
            } else {
                echo "Error refreshing token: $httpCode\n";
                echo "Response: $response\n";
                echo "cURL error: $curlError\n";
                exit;
            }
            curl_close($ch);
        }
        file_put_contents("etsy_{$shopId}.json", json_encode($etsyData, JSON_PRETTY_PRINT));
    }
}

// Function to make authenticated requests
function makeRequest($endpoint, $shopId) {
    $etsyData = json_decode(file_get_contents("etsy_{$shopId}.json"), true);
    $accessToken = $etsyData['access_token'];
    $apiUrl = "https://openapi.etsy.com/v3/application/{$endpoint}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "x-api-key: {$etsyData['client_id']}"
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data;
    } else {
        echo "Error: $httpCode\n";
        echo "Response: $response\n";
        return null;
    }
}

function getUserDetails($shopId) {
    $etsyData = json_decode(file_get_contents("etsy_{$shopId}.json"), true);
    $accessToken = $etsyData['access_token'];
    $apiUrl = "https://openapi.etsy.com/v3/application/users/me";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "x-api-key: {$etsyData['client_id']}"
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data;
    } else {
        echo "Error: $httpCode\n";
        echo "Response: $response\n";
        return null;
    }
}

function getActiveListings($shopId) {
    $allListings = [];
    $limit = 100;
    $offset = 0;
    do {
        $response = makeRequest("shops/{$shopId}/listings/active?limit={$limit}&offset={$offset}", $shopId);
        if ($response && isset($response['results'])) {
            foreach ($response['results'] as $listing) {
                $listing['inventory'] = getListingInventory($listing['listing_id'], $shopId);
                $listing['images'] = getListingImages($listing['listing_id'], $shopId);
                $listing['variation_images'] = getVariationImages($listing['listing_id'], $shopId);
                $allListings[] = $listing;
                usleep(100000);
                echo ".";
            }
            $offset += $limit;
        } else {
            break;
        }
        usleep(500000);
        echo "D";
    } while ($offset < $response['count']);
    echo "\n";
    return $allListings;
}

function getListingInventory($listingId, $shopId) {
    $response = makeRequest("listings/{$listingId}/inventory", $shopId);
    if ($response && isset($response['products'])) {
        return $response['products'];
    }
    return null;
}

function getListingImages($listingId, $shopId) {
    $response = makeRequest("listings/{$listingId}/images", $shopId);
    if ($response && isset($response['results'])) {
        return $response['results'];
    }
    return null;
}

function getVariationImages($listingId, $shopId) {
    $response = makeRequest("shops/{$shopId}/listings/{$listingId}/variation-images", $shopId);
    if ($response && isset($response['results'])) {
        return $response['results'];
    }
    return null;
}

function submitFile($shopId) {
    $file = 'c:/php/etsy_listings.json';
    $remoteUrl = ''; // URL to submit the file to
    $postData = [
        'file' => new CURLFile($file),
        'shop_id' => $shopId
    ];
    $t = 0;
    do {   
        $t++;         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($response === false || $httpCode >= 500) {
            $error = curl_error($ch);
            curl_close($ch);
            echo "cURL error: $error\n";
            if ($t > 10) {
                echo "Giving up.\n";
                return false;
            }
            sleep($t*60);
            continue;
        } else {
            curl_close($ch);
        }
        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        } elseif ($httpCode >= 400 && $httpCode < 500) {
            echo "Client error: $httpCode. Please check the request.\n";
            return false;
        }
    } while (true);
}

function execute($shopId) {
    if (file_exists("etsy_listings.json") && time() - filemtime("etsy_listings.json") < 86400) {
        echo "Listings are up to date.\n";
    } else {
        echo "Refreshing listings...\n";
        $activeListings = getActiveListings($shopId);
        echo count($activeListings) . " active listings found.\n";
        file_put_contents("etsy_listings.json", json_encode($activeListings, JSON_PRETTY_PRINT));
    }
    if (submitFile($shopId)) {
        echo "File submitted successfully.\n";
    } else {
        echo "Error submitting file.\n";
    }
}

$shopId = [
    'iwa' => [
        'user_id' => '',
        'shop_id' => '',
    ],
];

if (isset($argv[1]) && array_key_exists($argv[1], $shopId)) {
    $activeShop = $shopId[$argv[1]]['shop_id'];
} else {
    echo "No valid shop ID provided. Do you want to continue with a blank ID? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));

    if (strtolower($response) !== 'y') {
        echo "Operation aborted.\n";
        exit;
    }

    $activeShop = "";
}

init($activeShop);
$userDetails = getUserDetails($activeShop);
print_r($userDetails);

execute($activeShop);

*/