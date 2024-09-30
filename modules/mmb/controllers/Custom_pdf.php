<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Custom pdf Controller.
 *
 * This controller handles PDF customizer related functionalities in the admin panel.
 * It extends the AdminController class, which provides common admin functionality.
 */
class Custom_pdf extends AdminController
{
    /**
     * Constructor method.
     *
     * Initializes the Custom_pdf controller.
     * Invokes the parent constructor to inherit admin panel functionality.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load settings view to customize pdf.
     *
     * @param string $pdf_type the type of PDF (default is 'proposals')
     */
    public function settings($pdf_type = 'proposals')
    {
        $postData['pdf_type'] = $pdf_type;
        $postData['title'] = _l('custom_pdf_settings');
        $this->load->view('custom_pdf/settings/index', $postData);
    }

    /**
     * Handles the storage of PDF customizer's settings.
     */
    public function store()
    {
        // Get the POST data
        $postData = $this->input->post();
        if (empty($postData) || empty($_FILES)) {
            redirect(admin_url('mmb/custom_pdf/settings/'));
        }

        // Determine the redirect type
        $redirectType = implode(', ', array_keys($postData['settings']));

        // Handle file uploads and update settings
        foreach ($_FILES['settings']['name'] as $pdf => $upload_data) {
            $uploaded_keys = array_keys($upload_data);
            foreach ($uploaded_keys as $control_name) {
                $postData['settings'][$pdf][$control_name]['image'] = getPdfOptions($pdf, $control_name, 'image');
                if (isset($_FILES['settings']['name'][$pdf][$control_name]['image']) && '' != $_FILES['settings']['name'][$pdf][$control_name]['image']) {
                    $path = get_upload_path_by_type('custom_pdf_' . $pdf);

                    // Get the temp file path
                    $tmpFilePath = $_FILES['settings']['tmp_name'][$pdf][$control_name]['image'];

                    // Make sure we have a filepath
                    if (!empty($tmpFilePath) && '' != $tmpFilePath) {
                        // Getting file extension
                        $extension = strtolower(pathinfo($_FILES['settings']['name'][$pdf][$control_name]['image'], \PATHINFO_EXTENSION));
                        $allowed_extensions = [
                            'jpg',
                            'jpeg',
                            'png',
                        ];

                        if (!in_array($extension, $allowed_extensions)) {
                            set_alert('warning', 'Image extension not allowed.');
                            redirect(admin_url('mmb/custom_pdf/settings/' . $redirectType));
                        }

                        // Setup our new file path
                        $filename = $control_name . '.' . $extension;
                        $newFilePath = $path . $filename;

                        _maybe_create_upload_path($path);

                        // Upload the file into the company uploads dir
                        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                            $postData['settings'][$pdf][$control_name]['image'] = $filename;
                        }
                    }
                }

                $postKey = $pdf . '_' . $control_name . '_text';
                if ($this->input->post($postKey)) {
                    $postData['settings'][$pdf][$control_name]['text'] = html_purify($this->input->post($postKey, false));
                    unset($postData[$pdf . '_' . $control_name]);
                }
            }

            foreach ($postData['settings'][$pdf]['items_table'] as $key => $value) {
                $itemsTableStyle[] = [
                    'id' => $pdf . '_' . $key,
                    'color' => $value
                ];
            }

            $postData['settings'][$pdf . '_pdf_settings'] = json_encode($postData['settings'][$pdf]);
            unset($postData['settings'][$pdf]);

            update_option($pdf . '_item_table_custom_style', json_encode($itemsTableStyle));
        }

        // Load necessary models
        $this->load->model(['payment_modes_model', 'settings_model']);

        // Update settings and set success alert
        $success = $this->settings_model->update($postData);
        if ($success > 0) {
            set_alert('success', _l('settings_updated'));
        }

        // Redirect to the appropriate settings page
        redirect(admin_url('mmb/custom_pdf/settings/' . $redirectType));
    }

    /**
     * Handles the removal of an image associated with a PDF customizer section.
     *
     * @param string $type    the type of PDF
     * @param string $section the section of the PDF customizer
     */
    public function remove_pdf_image($type, $section)
    {
        // Get the PDF options from the database and decode them
        $pdfOptions = json_decode(get_option($type . '_pdf_settings'), true);

        // Define the path to the image file
        $path = get_upload_path_by_type('custom_pdf_' . $type) . $pdfOptions[$section]['image'];

        // Check if the file exists and delete it
        if (file_exists($path)) {
            unlink($path);
        }

        // Remove the image information from the PDF options
        unset($pdfOptions[$section]['image']);

        // Update the PDF options in the database
        update_option($type . '_pdf_settings', json_encode($pdfOptions));

        // Redirect back to the referring page
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function pdf($pdfType, $section)
    {
        try {
            $pdf = samplePdf($pdfType, $section);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (false !== strpos($message, 'Unable to get the size of the image')) {
                show_pdf_unable_to_get_image_size_error();
            }
            exit;
        }

        $type = 'I';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output($pdfType . '_sample.pdf', $type);
    }
}
