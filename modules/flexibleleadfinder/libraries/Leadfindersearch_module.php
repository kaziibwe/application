<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Leadfindersearch_module
{
    private $ci;
    private $client;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model(FLEXIBLELEADFINDER_MODULE_NAME . '/flexibleleadfindercontacts_model');
    }

    protected function query_places_api($leadfinder_id, $url, $params)
    {
        $query_string = http_build_query($params);
        $request_url = $url . '?' . $query_string;

        $response = file_get_contents($request_url);
        $data = json_decode($response, true);

        // echo "<pre>";
        // var_dump($data);
        // echo "</pre>";
        // die;

        if (isset($data['results'])) {
            $businesses = $data['results'];
            // $contacts = [];

            // We use this to avoid timeout due to
            // large number of results
            $limit = option_exists(FLEXIBLELEADFINDER_RECORDS_LIMIT_SETTING)
                ? get_option(FLEXIBLELEADFINDER_RECORDS_LIMIT_SETTING)
                : FLEXIBLELEADFINDER_MAX_LEADS;
            $limit = min([count($businesses), $limit]);

            for ($i = 0; $i < $limit; $i++) {
                $business = $businesses[$i];

                $placeId = $business['place_id'];
                if ($contact = $this->get_place_id_details($placeId)) {
                    $date = flexibleleadfinder_get_date();

                    $contact = array_merge($contact, [
                        'date_added' => $date,
                        'date_updated' => $date,
                        'leadfinder_id' => $leadfinder_id
                    ]);

                    $this->ci->flexibleleadfindercontacts_model->add($contact);
                }
            }

            if(isset($data['next_page_token']) && $data['next_page_token']){
                $params = array_merge($params, [
                    'pagetoken' => $data['next_page_token']
                ]);

                return $this->query_places_api($leadfinder_id, $url, $params);
            }else{
                return true;
            }
        } else {
            return false;
        }
    }

    public function populate_businesses_near_location($leadfinder_id, $address, $keyword)
    {
        $api_key = trim(get_option('google_api_key'));
        $radius = get_option('flexibleleadfinder_radius') ? get_option('flexibleleadfinder_radius') : 5000;
        // Convert address to coordinates using Geocoding API
        $geocode_url = "https://maps.googleapis.com/maps/api/geocode/json";
        $geocode_params = array(
            "address" => $address,
            "key" => $api_key
        );
        $geocode_query_string = http_build_query($geocode_params);
        $geocode_request_url = $geocode_url . '?' . $geocode_query_string;

        try {
            $geocode_response = file_get_contents($geocode_request_url);
            $geocode_data = json_decode($geocode_response, true);
            if (isset($geocode_data['results'][0]['geometry']['location'])) {
                $location = $geocode_data['results'][0]['geometry']['location']['lat'] . ',' . $geocode_data['results'][0]['geometry']['location']['lng'];

                // Perform nearby search using obtained coordinates
                $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";
                $params = array(
                    "key" => $api_key,
                    "location" => $location,
                    "radius" => $radius,  // Search within a 5km radius
                    //"keyword" => $keyword . ' with email address and phone number',
                    "keyword" => $keyword,
                    "fields" => "name,formatted_address,geometry,rating,formatted_phone_number,reviews,website"
                );

                return $this->query_places_api($leadfinder_id, $url, $params);
            } else {
                throw new Exception("Geocoding API could not find coordinates for the provided address.");
            }
        } catch (Exception $e) {
            throw new Exception("An error occurred: " . $e->getMessage());
        }
    }

    private function get_place_id_details($placeId)
    {
        $api_key = trim(get_option('google_api_key'));

        $apiEndpoint = 'https://maps.googleapis.com/maps/api/place/details/json';
        $params = [
            'place_id' => $placeId,
            'key' => $api_key,
        ];
        // Construct the URL
        $url = $apiEndpoint . '?' . http_build_query($params);

        // Make the API request
        $response = @file_get_contents($url);

        // Check if the request was successful
        if ($response === false) {
            //echo 'Error: Unable to fetch data from the Google Places API.';
            return false;
        } else {
            // Decode the JSON response
            $data = json_decode($response, true);

            // Check if the request was successful
            if ($data['status'] != 'OK') {
                throw new Exception('Error: ' . $data['status']);
            } else {

                if (!array_key_exists('website', $data['result'])) {
                    $website = "";
                    $emailAddress = "";
                } else {
                    $website = strtok($data['result']['website'], '?');
                    $emailAddress = $this->scrapeEmailsAndContactPageEmails($website);
                }

                $name = $data['result']['name'];
                $local_phone = array_key_exists('formatted_phone_number', $data['result']) ? $data['result']['formatted_phone_number'] : '';
                $international_phone = array_key_exists('international_phone_number', $data['result']) ? $data['result']['international_phone_number'] : '';
                $address = $data['result']['formatted_address'];
                $address_components = $data['result']['address_components'];

                $city = '';
                $state = '';
                $country = '';
                $postal_code = '';

                foreach ($address_components as $component) {
                    $types = $component['types'];
                    if (in_array('locality', $types)) {
                        $city = $component['long_name'];
                    } elseif (in_array('administrative_area_level_1', $types)) {
                        $state = $component['long_name'];
                    } elseif (in_array('country', $types)) {
                        $country = $component['long_name'];
                    } elseif (in_array('postal_code', $types)) {
                        $postal_code = $component['long_name'];
                    }
                }

                $contact_details = [
                    'name' => $name,
                    'website' => $website,
                    'phone' => $local_phone ?? '',
                    'phonenumber' => $international_phone ?? '',
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'country' => $country,
                    'postal_code' => $postal_code,
                    'email' => ''
                ];

                if ($emailAddress) {
                    return array_merge($contact_details, [
                        'email' => implode(', ', $emailAddress),
                    ]);
                } else if (get_option(FLEXIBLELEADFINDER_IMPORT_RESULTS_WITHOUT_EMAIL_SETTING)) {
                    return $contact_details;
                } else {
                    return false;
                }
            }
        }

    }

    private function scrapeEmailsAndContactPageEmails($url)
    {

        try {
            //strip url of query string
            // Fetch the HTML content of the webpage
            $html = @file_get_contents($url);
            // Regular expression to match email addresses
            //$pattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';
            $pattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';


            // Match email addresses in the HTML content
            preg_match_all($pattern, $html, $matches);

            // Extract unique email addresses
            $emails = array_unique($matches[0]);

            //we don't need to go further if we have emails or if we are importing all businesses
            if (count($emails) > 0 || get_option(FLEXIBLELEADFINDER_IMPORT_RESULTS_WITHOUT_EMAIL_SETTING) == 1) {
                return $emails;
            }
            // Find links to other pages (Contact Us, About Us, etc.)
            $dom = new DOMDocument();
            @$dom->loadHTML($html); // Suppress warnings

            $links = [];
            $anchors = $dom->getElementsByTagName('a');
            foreach ($anchors as $anchor) {
                $href = $anchor->getAttribute('href');
                if ($href && strpos($href, 'http') === 0) { // Absolute URLs only
                    $links[] = $href;
                } elseif ($href && strpos($href, '/') === 0) { // Relative URLs
                    $links[] = rtrim($url, '/') . $href;
                }
            }

            // Fetch and extract email addresses from linked pages
            $linkedEmails = [];
            foreach ($links as $link) {
                $linkedHtml = @file_get_contents($link); // Suppress warnings
                if ($linkedHtml !== false) {
                    preg_match_all($pattern, $linkedHtml, $linkedMatches);
                    $linkedEmails = array_merge($linkedEmails, $linkedMatches[0]);
                    if (count($linkedEmails) > 0) {
                        break;
                    }
                }
            }

            // Combine and return all email addresses
            return array_unique(array_merge($emails, $linkedEmails));
        } catch (\Throwable $th) {
            // Catch any errors
            // and return empty to continue processing
            return '';
        }
    }
}