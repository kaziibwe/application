<?php

defined('BASEPATH') || exit('No direct script access allowed');
/**
 * Prepares common HTTP header names as choices.
 *
 * @return [Array]
 */
if (!function_exists('get_header_choices')) {
    function get_header_choices(): array
    {
        return [
            [
                'label' => 'Accept',
                'value' => 'Accept',
            ],
            [
                'label' => 'Accept-Charset',
                'value' => 'Accept-Charset',
            ],
            [
                'label' => 'Accept-Encoding',
                'value' => 'Accept-Encoding',
            ],
            [
                'label' => 'Accept-Language',
                'value' => 'Accept-Language',
            ],
            [
                'label' => 'Accept-Datetime',
                'value' => 'Accept-Datetime',
            ],
            [
                'label' => 'Authorization',
                'value' => 'Authorization',
            ],
            [
                'label' => 'Cache-Control',
                'value' => 'Cache-Control',
            ],
            [
                'label' => 'Connection',
                'value' => 'Connection',
            ],
            [
                'label' => 'Cookie',
                'value' => 'Cookie',
            ],
            [
                'label' => 'Content-Length',
                'value' => 'Content-Length',
            ],
            [
                'label' => 'Content-Type',
                'value' => 'Content-Type',
            ],
            [
                'label' => 'Date',
                'value' => 'Date',
            ],
            [
                'label' => 'Expect',
                'value' => 'Expect',
            ],
            [
                'label' => 'Forwarded',
                'value' => 'Forwarded',
            ],
            [
                'label' => 'From',
                'value' => 'From',
            ],
            [
                'label' => 'Host',
                'value' => 'Host',
            ],
            [
                'label' => 'If-Match',
                'value' => 'If-Match',
            ],
            [
                'label' => 'If-Modified-Since',
                'value' => 'If-Modified-Since',
            ],
            [
                'label' => 'If-None-Match',
                'value' => 'If-None-Match',
            ],
            [
                'label' => 'If-Range',
                'value' => 'If-Range',
            ],
            [
                'label' => 'If-Unmodified-Since',
                'value' => 'If-Unmodified-Since',
            ],
            [
                'label' => 'Max-Forwards',
                'value' => 'Max-Forwards',
            ],
            [
                'label' => 'Origin',
                'value' => 'Origin',
            ],
            [
                'label' => 'Pragma',
                'value' => 'Pragma',
            ],
            [
                'label' => 'Proxy-Authorization',
                'value' => 'Proxy-Authorization',
            ],
            [
                'label' => 'Range',
                'value' => 'Range',
            ],
            [
                'label' => 'Referer',
                'value' => 'Referer',
            ],
            [
                'label' => 'TE',
                'value' => 'TE',
            ],
            [
                'label' => 'User-Agent',
                'value' => 'User-Agent',
            ],
            [
                'label' => 'Upgrade',
                'value' => 'Upgrade',
            ],
            [
                'label' => 'Via',
                'value' => 'Via',
            ],
            [
                'label' => 'Warning',
                'value' => 'Warning',
            ],
            [
                'label' => 'custom',
                'value' => 'Custom',
            ],
        ];
    }
}

/**
 * Prepares common HTTP Request Method.
 *
 * @return [Array]
 */
if (!function_exists('get_request_method')) {
    function get_request_method(): array
    {
        return [
            [
                'label' => 'GET',
                'value' => 'GET',
            ],
            [
                'label' => 'POST',
                'value' => 'POST',
            ],
            [
                'label' => 'PUT',
                'value' => 'PUT',
            ],
            [
                'label' => 'PATCH',
                'value' => 'PATCH',
            ],
            [
                'label' => 'DELETE',
                'value' => 'DELETE',
            ],
        ];
    }
}

/**
 * Prepares common HTTP Request Formate.
 *
 * @return [Array]
 */
if (!function_exists('get_request_format')) {
    function get_request_format(): array
    {
        return [
            [
                'label' => 'JSON',
                'value' => 'JSON',
            ],
            [
                'label' => 'FORM',
                'value' => 'FORM',
            ],
        ];
    }
}

/**
 * [get_webhook_triggers Prepares an array of webhooks triggers].
 *
 * @return [Array]
 */
if (!function_exists('get_webhook_triggers')) {
    function get_webhook_triggers(): array
    {
        return [
            [
                'value'   => 'leads',
                'label'   => _l('lead'),
                'subtext' => _l('triggers_when_new_lead_created'),
            ],
            [
                'value'   => 'client',
                'label'   => _l('contact'),
                'subtext' => _l('triggers_when_new_contact_created'),
            ],
            [
                'value'   => 'invoice',
                'label'   => _l('invoice'),
                'subtext' => _l('triggers_when_new_invoice_created'),
            ],
            [
                'value'   => 'tasks',
                'label'   => _l('task'),
                'subtext' => _l('triggers_when_new_task_created'),
            ],
            [
                'value'   => 'projects',
                'label'   => _l('project'),
                'subtext' => _l('triggers_when_new_project_created'),
            ],
            [
                'value'   => 'proposals',
                'label'   => _l('proposal'),
                'subtext' => _l('triggers_when_new_proposal_created'),
            ],
            [
                'value'   => 'ticket',
                'label'   => _l('ticket'),
                'subtext' => _l('triggers_when_new_ticket_created'),
            ],
            [
                'value'   => 'invoice_payments',
                'label'   => _l('payments'),
                'subtext' => _l('triggers_when_new_payment_created'),
            ],
            [
                'value'   => 'staff',
                'label'   => _l('staff'),
                'subtext' => _l('triggers_when_new_staff_created'),
            ],
            [
                'value'   => 'contract',
                'label'   => _l('contracts'),
                'subtext' => _l('triggers_when_new_contract_created'),
            ],
            [
                'value'   => 'estimate',
                'label'   => _l('estimates'),
                'subtext' => _l('triggers_when_new_estimate_created'),
            ],
            [
                'value'   => 'event',
                'label'   => _l('calendar_event'),
                'subtext' => _l('triggers_when_new_calendar_event_created'),
            ]
        ];
    }
}

/**
 * [remove_blank_value remove blank values and return array].
 *
 * @param  [array] $var
 * @param  [string] $key_to_check
 *
 * @return [array]
 */
if (!function_exists('remove_blank_value')) {
    function remove_blank_value($var, $key_to_check): array
    {
        $data = [];
        foreach ($var as $key => $value) {
            if ('' === $value[$key_to_check]) {
                unset($var[$key]);
                continue;
            }
            $data[] = $value;
        }

        return $data;
    }
}

if (!function_exists('validate_request_url')) {
    function validate_request_url($request_url)
    {
        $sanitized_setting_value = get_sanitized_request_url($request_url);

        return filter_var($sanitized_setting_value, \FILTER_VALIDATE_URL);
    }
}

if (!function_exists('get_sanitized_request_url')) {
    function get_sanitized_request_url($request_url)
    {
        $sanitized_value = esc_url_with_merge_tags(encode_spaces_in_url($request_url));

        return is_url_without_scheme($request_url) ? "http://{$sanitized_value}" : $sanitized_value;
    }
}

if (!function_exists('esc_url_with_merge_tags')) {
    function esc_url_with_merge_tags($request_url)
    {
        return preg_replace('|[^a-z0-9-~+_.?#=!&;,/{:}%@$\|*\'()\[\]\\x80-\\xff]|i', '', $request_url);
    }
}

if (!function_exists('encode_spaces_in_url')) {
    function encode_spaces_in_url($request_url)
    {
        return str_replace(' ', '%20', wh_strip_all_tags($request_url, true));
    }
}

if (!function_exists('wh_strip_all_tags')) {
    function wh_strip_all_tags($string, $remove_breaks = false)
    {
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
        $string = strip_tags($string);

        if ($remove_breaks) {
            $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
        }

        return trim($string);
    }
}

if (!function_exists('is_url_without_scheme')) {
    function is_url_without_scheme($request_url)
    {
        $parsed_url = wh_parse_url($request_url);

        if (rgar($parsed_url, 'host') && !rgar($parsed_url, 'scheme')) {
            return true;
        }

        $host = explode('/', rgar($parsed_url, 'path'));

        return
            false !== filter_var(gethostbyname($host[0]), \FILTER_VALIDATE_IP)
            && !rgar($parsed_url, 'scheme');
    }
}

if (!function_exists('rgar')) {
    function rgar($array, $name)
    {
        if (isset($array[$name])) {
            return $array[$name];
        }

        return '';
    }
}

if (!function_exists('wh_parse_url')) {
    function wh_parse_url($url, $component = -1)
    {
        $to_unset = [];
        $url      = (string) $url;

        if ('//' === substr($url, 0, 2)) {
            $to_unset[] = 'scheme';
            $url        = 'placeholder:' . $url;
        } elseif ('/' === substr($url, 0, 1)) {
            $to_unset[] = 'scheme';
            $to_unset[] = 'host';
            $url        = 'placeholder://placeholder' . $url;
        }

        $parts = parse_url($url);

        if (false === $parts) {
            // Parsing failure.
            return $parts;
        }

        // Remove the placeholder values.
        foreach ($to_unset as $key) {
            unset($parts[$key]);
        }

        return _get_component_from_parsed_url_array($parts, $component);
    }
}

if (!function_exists('_get_component_from_parsed_url_array')) {
    function _get_component_from_parsed_url_array($url_parts, $component = -1)
    {
        if (-1 === $component) {
            return $url_parts;
        }

        $key = _wh_translate_php_url_constant_to_key($component);
        if (false !== $key && is_array($url_parts) && isset($url_parts[$key])) {
            return $url_parts[$key];
        }

        return null;
    }
}

if (!function_exists('_wh_translate_php_url_constant_to_key')) {
    function _wh_translate_php_url_constant_to_key($constant)
    {
        $translation = [
            \PHP_URL_SCHEME   => 'scheme',
            \PHP_URL_HOST     => 'host',
            \PHP_URL_PORT     => 'port',
            \PHP_URL_USER     => 'user',
            \PHP_URL_PASS     => 'pass',
            \PHP_URL_PATH     => 'path',
            \PHP_URL_QUERY    => 'query',
            \PHP_URL_FRAGMENT => 'fragment',
        ];

        if (isset($translation[$constant])) {
            return $translation[$constant];
        }

        return false;
    }
}

if (!function_exists('isJson')) {
    function isJson($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
}

if (!function_exists('isXml')) {
    function isXml($string)
    {
        $prev = libxml_use_internal_errors(true);

        $doc = simplexml_load_string($string);
        $errors = libxml_get_errors();

        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        return (empty($errors)) ? true : false;
    }
}

/*End of file "webhooks_helper.".php */
