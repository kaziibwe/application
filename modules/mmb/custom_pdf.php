<?php
defined('BASEPATH') || exit('No direct script access allowed');
/*
 * Define module name
 * Module Name Must be in CAPITAL LETTERS
 */
define('CUSTOM_PDF_MODULE', 'mmb');

define('CUSTOM_PDF_CONTRACT', FCPATH . 'uploads/custom_pdf/contract/');
define('CUSTOM_PDF_INVOICE', FCPATH . 'uploads/custom_pdf/invoice/');
define('CUSTOM_PDF_PROPOSAL', FCPATH . 'uploads/custom_pdf/proposals/');
define('CUSTOM_PDF_CREDIT_NOTE', FCPATH . 'uploads/custom_pdf/credit_note/');
define('CUSTOM_PDF_ESTIMATE', FCPATH . 'uploads/custom_pdf/estimate/');
define('CUSTOM_PDF_PAYMENT', FCPATH . 'uploads/custom_pdf/payment/');
define('APP_PDF_MARGIN_BOTTOM', 35);

// require_once __DIR__ . '/vendor/autoload.php';

_maybe_create_upload_path(FCPATH . 'uploads/custom_pdf/');

/*
 * Register language files, must be registered if the module is using languages
 */
register_language_files(CUSTOM_PDF_MODULE, ["custom_pdf"]);

get_instance()->load->helper(CUSTOM_PDF_MODULE . '/custom_pdf');

hooks()->add_action('app_admin_head', 'custom_pdf_add_head_components');
function custom_pdf_add_head_components()
{
    custom_pdf_items_table_custom_style_render();
}

hooks()->add_action('app_admin_footer', function () {
    // Check if the 'custom_pdf' module is active
    if (get_instance()->app_modules->is_active(CUSTOM_PDF_MODULE)) {
        // Generate the URL for the 'custom_pdf.js' script file
        $script_url = module_dir_url(CUSTOM_PDF_MODULE, 'assets/js/custom_pdf/custom_pdf.js');

        // Get the core version from the application's scripts
        $core_version = get_instance()->app_scripts->core_version();

        // Echo the script tag to include 'custom_pdf.js' with a version parameter
        echo '<script src="' . $script_url . '?v=' . $core_version . '"></script>';
    }
});

hooks()->add_action('admin_init', function () {
    get_instance()->app_menu->add_setup_menu_item('custom_pdf', [
        'slug' => 'custom_pdf_settinfs',
        'name' => _l('custom_pdf'),
        'icon' => '',
        'href' => admin_url('mmb/custom_pdf/settings'),
        'position' => 35,
    ]);
});

$custom_pdf = ['contract', 'invoice', 'proposal', 'credit_note', 'estimate', 'payment'];
foreach ($custom_pdf as $pdf) {
    pdf_class_path($pdf);
}

function pdf_class_path($type)
{
    hooks()->add_action($type . '_pdf_class_path', function ($path) use ($type) {
        $path = __DIR__ . '/libraries/pdf/' . ucwords($type) . '_pdf.php';

        return $path;
    });
}

hooks()->add_filter('get_upload_path_by_type', function ($path, $type) {
    switch ($type) {
        case 'custom_pdf_contract':
            $path = CUSTOM_PDF_CONTRACT;
            break;

        case 'custom_pdf_invoice':
            $path = CUSTOM_PDF_INVOICE;
            break;

        case 'custom_pdf_proposals':
            $path = CUSTOM_PDF_PROPOSAL;
            break;

        case 'custom_pdf_credit_note':
            $path = CUSTOM_PDF_CREDIT_NOTE;
            break;

        case 'custom_pdf_estimate':
            $path = CUSTOM_PDF_ESTIMATE;
            break;

        case 'custom_pdf_payment':
            $path = CUSTOM_PDF_PAYMENT;
            break;
    }

    return $path;
}, 10, 2);
