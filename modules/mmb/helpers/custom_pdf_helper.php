<?php

// Check if the function 'getPdfOptions' does not already exist
if (!function_exists('getPdfOptions')) {
    /**
     * Retrieve PDF options from the specified settings, based on section and field.
     *
     * @param string $type    the type of PDF settings to retrieve
     * @param string $section the section within the PDF settings
     * @param string $field   the field within the specified section
     *
     * @return mixed the value of the specified PDF option, or an empty string if not found
     */
    function getPdfOptions($type, $section, $field)
    {
        // Retrieve the JSON-encoded PDF settings for the specified type
        $pdfOptions = json_decode(get_option($type.'_pdf_settings'), true);

        // Use the specified section and field to access the desired PDF option
        // If not found, return an empty string as a default value
        return $pdfOptions[$section][$field] ?? '';
    }
}

// Check if the function 'parsePDFMergeFields' does not already exist
if (!function_exists('parsePDFMergeFields')) {
    /**
     * Parse PDF merge fields from a text.
     *
     * @param string $type The type of merge fields (e.g., 'contract').
     * @param string $text the text containing merge field placeholders
     * @param object $data the data object containing values for replacements
     *
     * @return string the text with merge field placeholders replaced with values
     */
    function parsePDFMergeFields($type, $text, $data = '')
    {
        $merge_field_type = ($type == 'payment') ? 'invoice' : $type;
        // Retrieve merge fields for the specified type
        $mergeFields = get_instance()->app_mail_template->set_merge_fields($merge_field_type.'_merge_fields', !empty($data) ? $data->id : 0)->merge_fields;

        // Loop through data and replace placeholders
        foreach ($mergeFields as $key => $value) {
            // Check if the placeholder exists in the text and value is not null
            $text = str_replace($key, $value ?? '', $text);
        }

        return $text;
    }
}

if (!function_exists('getRecommendedDimensions')) {
    /**
     * Get the recommended dimensions for a PDF section based on format and section.
     *
     * @param string $pdf_type the PDF format type
     * @param string $section  The section (e.g., 'page' or 'header').
     *
     * @return string the recommended size in pixels or a message if not found
     */
    function getRecommendedDimensions($pdf_type, $section)
    {
        // Retrieve the dimensions data and create a collection.
        $dimension = collect(getDimension());

        // Use the collection to filter and find the recommended size.
        $result = $dimension
            ->where('format', get_option('pdf_format_'.$pdf_type)) // Filter by format
            ->where('section', $section) // Filter by section
            ->pluck('size') // Extract the 'size' field
            ->first(); // Get the first matching size

        // Return the found size or a default message if no match is found.
        return $result ?: 'Size not found';
    }
}

/*
 * Retrieve predefined dimensions for different PDF formats and sections.
 *
 * @return array An array of format-size-section combinations.
 */
if (!function_exists('getDimension')) {
    function getDimension()
    {
        return [
            // A4 Portrait dimensions for page and header sections
            [
                'format'  => 'A4-PORTRAIT',
                'size'    => '595 x 842px',
                'section' => 'page',
            ],
            [
                'format'  => 'A4-PORTRAIT',
                'size'    => '595 x 113px',
                'section' => 'header',
            ],

            // A4 Landscape dimensions for page and header sections
            [
                'format'  => 'A4-LANDSCAPE',
                'size'    => '842 x 595px',
                'section' => 'page',
            ],
            [
                'format'  => 'A4-LANDSCAPE',
                'size'    => '842 x 113px',
                'section' => 'header',
            ],

            // Letter Portrait dimensions for page and header sections
            [
                'format'  => 'LETTER-PORTRAIT',
                'size'    => '612 x 792px',
                'section' => 'page',
            ],
            [
                'format'  => 'LETTER-PORTRAIT',
                'size'    => '612 x 113px',
                'section' => 'header',
            ],

            // Letter Landscape dimensions for page and header sections
            [
                'format'  => 'LETTER-LANDSCAPE',
                'size'    => '792 x 612px',
                'section' => 'page',
            ],
            [
                'format'  => 'LETTER-LANDSCAPE',
                'size'    => '792 x 113px',
                'section' => 'header',
            ],
        ];
    }
}

if (!function_exists('samplePdf')) {
    function samplePdf($pdfType, $section, $tag = '')
    {
        return app_pdf('sample_pdf', module_libs_path(CUSTOM_PDF_MODULE, 'pdf/'.'Sample_pdf'), $pdfType, $section, $tag);
    }
}

if (!function_exists('removeScriptTags')) {
    // Function to remove script tags
    function removeScriptTags($input)
    {
        $filteredInput = str_ireplace('script', '', $input);
        $filteredInput = str_ireplace('alert', '', $filteredInput);

        return $filteredInput;
    }
}

if (!function_exists('customPdfItemsTableData')) {
    function customPdfItemsTableData($transaction, $type, $for = 'html', $admin_preview = false)
    {
        $className = ucfirst($type) . '_items_table';

        include_once(module_libs_path(CUSTOM_PDF_MODULE, 'items_table/' . $className . '.php'));

        $class = new $className($transaction, $type, $for, $admin_preview);

        if (!$class instanceof App_items_table_template) {
            show_error(get_class($class) . ' must be instance of "App_items_template"');
        }

        return $class;
    }
}

if (!function_exists('get_custom_pdf_items_table_styling_areas')) {
    function get_custom_pdf_items_table_styling_areas()
    {
        $types = ['proposals', 'estimate', 'invoice', 'credit_note', 'payment'];

        $all_data = [];

        foreach ($types as $pdf_types) {
            $all_data[] = [
                [
                    'id'                   => $pdf_types . '_heading_row_bg_color',
                    'target'               => '.' . $pdf_types . '_sample_table thead tr',
                    'css'                  => 'background-color',
                    'additional_selectors' => '',
                ],
                [
                    'id'                   => $pdf_types . '_heading_row_text_color',
                    'target'               => '.' . $pdf_types . '_sample_table thead tr',
                    'css'                  => 'color',
                    'additional_selectors' => '',
                ],
                [
                    'id'                   => $pdf_types . '_odd_rows_bg_color',
                    'target'               => '.' . $pdf_types . '_sample_table tbody tr:first-child',
                    'css'                  => 'background-color',
                    'additional_selectors' => '',
                ],
                [
                    'id'                   => $pdf_types . '_odd_rows_text_color',
                    'target'               => '.' . $pdf_types . '_sample_table tbody tr:first-child>td',
                    'css'                  => 'color',
                    'additional_selectors' => '',
                ],
                [
                    'id'                   => $pdf_types . '_even_rows_bg_color',
                    'target'               => '.' . $pdf_types . '_sample_table tbody tr:nth-child(2)',
                    'css'                  => 'background-color',
                    'additional_selectors' => '',
                ],
                [
                    'id'                   => $pdf_types . '_even_rows_text_color',
                    'target'               => '.' . $pdf_types . '_sample_table tbody tr:nth-child(2)>td',
                    'css'                  => 'color',
                    'additional_selectors' => '',
                ],
                [
                    'id'                   => $pdf_types . '_total_row_bg_color',
                    'target'               => '.' . $pdf_types . '_sample_table tbody tr:nth-child(3)',
                    'css'                  => 'background-color',
                    'additional_selectors' => '',
                ],
                [
                    'id'                   => $pdf_types . '_total_row_text_color',
                    'target'               => '.' . $pdf_types . '_sample_table tbody tr:nth-child(3)>td',
                    'css'                  => 'color',
                    'additional_selectors' => '',
                ]
            ];
        }

        $areas = [];

        foreach ($all_data as $subArray) {
            foreach ($subArray as $item) {
                $areas[] = $item;
            }
        }

        return $areas;
    }
}

if (!function_exists('get_custom_pdf_items_table_applied_styling_area')) {
    function get_custom_pdf_items_table_applied_styling_area()
    {
        $types = ['proposals', 'estimate', 'invoice', 'credit_note', 'payment'];

        // Get the applied custom table styling area values
        $proposals_table_style = get_option('proposals_item_table_custom_style');
        if ('' == $proposals_table_style) {
            // return [];
        }
        $proposals_table_style = json_decode($proposals_table_style);

        // Get the applied custom table styling area values
        $estimate_table_style = get_option('estimate_item_table_custom_style');
        if ('' == $estimate_table_style) {
            // return [];
        }
        $estimate_table_style = json_decode($estimate_table_style);

        // Get the applied custom table styling area values
        $invoice_table_style = get_option('invoice_item_table_custom_style');
        if ('' == $invoice_table_style) {
            // return [];
        }
        $invoice_table_style = json_decode($invoice_table_style);

        // Get the applied custom table styling area values
        $credit_note_table_style = get_option('credit_note_item_table_custom_style');
        if ('' == $credit_note_table_style) {
            // return [];
        }
        $credit_note_table_style = json_decode($credit_note_table_style);

        // Get the applied custom table styling area values
        $payment_table_style = get_option('payment_item_table_custom_style');
        if ('' == $payment_table_style) {
            // return [];
        }
        $payment_table_style = json_decode($payment_table_style);

        return array_merge($proposals_table_style ?? [], $estimate_table_style ?? [], $invoice_table_style ?? [], $credit_note_table_style ?? [], $payment_table_style ?? []);
    }
}

// Append custom style for table based on applied styling
if (!function_exists('custom_pdf_items_table_custom_style_render')) {
    function custom_pdf_items_table_custom_style_render()
    {
        $theme_style   = get_custom_pdf_items_table_applied_styling_area();
        $styling_areas = get_custom_pdf_items_table_styling_areas();

        foreach ($styling_areas as $type => $_area) {
            foreach ($theme_style as $applied_style) {
                if ($applied_style->id == $_area['id']) {
                    // Output custom style CSS for a table element
                    echo '<style class="custom_style_' . $_area['id'] . '">';
                    echo $_area['target'] . '{';
                    echo $_area['css'] . ':' . $applied_style->color . ' !important ;';
                    echo '}';
                    echo '</style>';
                }
            }
        }
    }
}
