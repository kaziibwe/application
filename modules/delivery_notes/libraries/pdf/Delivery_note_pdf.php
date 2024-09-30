<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(LIBSPATH . 'pdf/App_pdf.php');

class Delivery_note_pdf extends App_pdf
{
    protected $delivery_note;

    private $delivery_note_number;

    public function __construct($delivery_note, $tag = '')
    {
        $this->load_language($delivery_note->clientid);

        $delivery_note                = hooks()->apply_filters('delivery_note_html_pdf_data', $delivery_note);
        $GLOBALS['delivery_note_pdf'] = $delivery_note;

        parent::__construct();

        $this->tag             = $tag;
        $this->delivery_note        = $delivery_note;
        $this->delivery_note_number = format_delivery_note_number($this->delivery_note->id);

        $this->SetTitle($this->delivery_note_number);
    }

    public function prepare()
    {
        $this->with_number_to_word($this->delivery_note->clientid);

        $this->set_view_vars([
            'status'          => $this->delivery_note->status,
            'delivery_note_number' => $this->delivery_note_number,
            'delivery_note'        => $this->delivery_note,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'delivery_note';
    }

    protected function file_path()
    {
        return module_dir_path(DELIVERY_NOTE_MODULE_NAME, 'views/pdf/delivery_note_pdf.php');
    }

    /**
     * Append all signatures to PDF.
     * It add system pdf when enabled, staff pdf and client pdf
     *
     * @return void
     */
    public function process_signature1()
    {
        $record = $this->delivery_note;

        $signatures = [];

        // Insert the system signature if allowed
        if (get_option('show_pdf_signature_delivery_note') == 1) {
            $signatureImage = get_option('signature_image');
            $signaturePath   = get_upload_path_by_type('company') . $signatureImage;
            $company_signature = (object)[
                'signature_title' => _l('authorized_signature_text'),
                'signature' => $signaturePath
            ];
            $signatures[] = $company_signature;
        }

        // Add staff signatures
        $signatures = array_merge($signatures, $record->staff_signatures);

        // Insert customer signature if signed
        $customer_signature = $record;
        $customer_signature->signature_title = _l('document_customer_signature_text');
        if (!empty($record->signature)) {
            $customerSignatureImage = get_upload_path_by_type($this->type()) . $record->id . '/' . $record->signature;
            $customerSignatureImage = hooks()->apply_filters('pdf_customer_signature_image_path', $customerSignatureImage, $this->type());
            $customer_signature->signature = $customerSignatureImage;
        }
        unset($customer_signature->staff_signatures);
        $signatures[] = $customer_signature;


        // Make filtering hooks for modules
        $hookData = [
            'pdf_instance'       => $this,
            'type'               => $this->type(),
            'signatures'         => $signatures
        ];
        $signatures = hooks()->apply_filters('delivery_note_pdf_signatures', $hookData)['signatures'];

        $signatory_allowed_fields = get_option('delivery_note_signatory_allowed_fields');
        $signatory_allowed_fields = empty($signatory_allowed_fields) ? [] : (array)json_decode($signatory_allowed_fields);
        $signatory_allowed_fields = hooks()->apply_filters('delivery_note_pdf_signatory_fields', $signatory_allowed_fields);

        // Render signatures
        if (!empty($signatures)) {
        }
    }

    /**
     * Append all signatures to PDF.
     * It add system pdf when enabled, staff pdf and client pdf
     *
     * @return void
     */
    public function process_signature()
    {
        $record = $this->delivery_note;

        $signatures = [];

        // Insert the system signature if allowed
        if (get_option('show_pdf_signature_delivery_note') == 1) {
            $signatureImage = get_option('signature_image');
            $signaturePath   = get_upload_path_by_type('company') . $signatureImage;
            $company_signature = (object)[
                'signature_title' => _l('authorized_signature_text'),
                'signature' => $signaturePath
            ];
            $signatures[] = $company_signature;
        }

        // Add staff signatures
        $signatures = array_merge($signatures, $record->staff_signatures);

        // Insert customer signature if signed
        $customer_signature = $record;
        $customer_signature->signature_title = _l('document_customer_signature_text');
        if (!empty($record->signature)) {
            $customerSignatureImage = get_upload_path_by_type($this->type()) . $record->id . '/' . $record->signature;
            $customerSignatureImage = hooks()->apply_filters('pdf_customer_signature_image_path', $customerSignatureImage, $this->type());
            $customer_signature->signature = $customerSignatureImage;
        }
        unset($customer_signature->staff_signatures);
        $signatures[] = $customer_signature;


        // Make filtering hooks for modules
        $hookData = [
            'pdf_instance'       => $this,
            'type'               => $this->type(),
            'signatures'         => $signatures
        ];
        $signatures = hooks()->apply_filters('delivery_note_pdf_signatures', $hookData)['signatures'];

        $signatory_allowed_fields = get_option('delivery_note_signatory_allowed_fields');
        $signatory_allowed_fields = empty($signatory_allowed_fields) ? [] : (array)json_decode($signatory_allowed_fields);
        $signatory_allowed_fields = hooks()->apply_filters('delivery_note_pdf_signatory_fields', $signatory_allowed_fields);

        $layout = get_option('delivery_notes_signature_layout');

        // Render signatures
        if (!empty($signatures)) {
            if ($layout == 'grid')
                $this->render_signatures_grid($signatures, $signatory_allowed_fields, $record);
            else
                $this->render_signatures($signatures, $signatory_allowed_fields, $record);
        }
    }

    /**
     * Render signatures in linear format.
     * One signature in a row
     *
     * @param array $signatures
     * @param array $signatory_allowed_fields
     * @param object $record
     * @return void
     */
    protected function render_signatures($signatures, $signatory_allowed_fields, $record)
    {
        foreach ($signatures as $sign) {
            $dimensions       = $this->getPageDimensions();

            $path = $sign->signature ?? '';

            if (!file_exists($path) && !empty($path)) {
                $path = get_upload_path_by_type($this->type()) . $record->id . '/' . $sign->signature;
                $path = hooks()->apply_filters('pdf_signature_image_path', $path, $this->type());
            }

            $signature = "<span>{$sign->signature_title}</span>";

            if (isset($sign->acceptance_firstname)) {
                $signature .= '<br/><br/><span style="font-weight:bold;text-align: left;">';
                if (in_array('name', $signatory_allowed_fields))
                    $signature .= _l('document_signed_by') . ": {$sign->acceptance_firstname} {$sign->acceptance_lastname}<br />";
                if (in_array('date', $signatory_allowed_fields))
                    $signature .= _l('document_signed_date') . ': ' . _dt($sign->acceptance_date ?? $sign->datecreated) . '<br />';
                if (in_array('ip', $signatory_allowed_fields))
                    $signature .= _l('document_signed_ip') . ": {$sign->acceptance_ip}";
                $signature .= '</span>';
            } else {
                $signature .= '<br/><br/><br/><br/>';
            }

            $signature .= '<br />';

            $signature .= str_repeat(
                '<br />',
                hooks()->apply_filters('pdf_signature_break_lines', 1)
            );

            $width = ($dimensions['wk'] / 2) - $dimensions['lm'];
            $this->MultiCell($width, 0, $signature, 0, 'J', 0, 0, '', '', true, 0, true, true, 0);

            $canWriteImage = !empty($path) && file_exists($path) && !is_dir($path);

            // Write image if possible
            if ($canWriteImage) {
                $imageData = file_get_contents($path);
                $staffSignatureSize = hooks()->apply_filters('customer_staff_signature_size', 0);
                $this->Image('@' . $imageData, $this->getX(), $this->getY(), $staffSignatureSize, 0, 'PNG', '', 'R', true, 300, 'R', false, false, 0, true);
                $this->ln(36);
            }

            // Write empty line for putting signature manaually if no image exist for the signature
            if (!$canWriteImage) {
                $blankSignatureLine = hooks()->apply_filters('blank_signature_line', '_________________________');
                $blankSignatureLine =  str_repeat('<br />', hooks()->apply_filters('pdf_signature_break_lines', 6)) . $blankSignatureLine;
                $this->MultiCell($width, 0, $blankSignatureLine, 0, 'R', 0, 1, '', '', true, 0, true, false, 0);
                $this->ln(18);
            }
        }
    }

    /**
     * Render signatures in grid/column format
     * Two signature in a row
     *
     * @param array $signatures
     * @param array $signatory_allowed_fields
     * @param object $record
     * @return void
     */
    protected function render_signatures_grid($signatures, $signatory_allowed_fields, $record)
    {
        $default_line_breaks = 1;
        $gridGap = 12;

        foreach ($signatures as $index => $sign) {
            $dimensions       = $this->getPageDimensions();

            $path = $sign->signature ?? '';

            if (!file_exists($path) && !empty($path)) {
                $path = get_upload_path_by_type($this->type()) . $record->id . '/' . $sign->signature;
                $path = hooks()->apply_filters('pdf_signature_image_path', $path, $this->type());
            }

            $signature = $sign->signature_title;

            if (isset($sign->acceptance_firstname)) {
                $signature .= '<br/><br/>';
                if (in_array('name', $signatory_allowed_fields))
                    $signature .= _l('document_signed_by') . ": {$sign->acceptance_firstname} {$sign->acceptance_lastname}<br />";
                if (in_array('date', $signatory_allowed_fields))
                    $signature .= _l('document_signed_date') . ': ' . _dt($sign->acceptance_date ?? $sign->datecreated) . '<br />';
                if (in_array('ip', $signatory_allowed_fields))
                    $signature .= _l('document_signed_ip') . ": {$sign->acceptance_ip}";
            } else {
                $signature .= '<br/><br/><br/><br/>';
            }


            $canWriteImage = !empty($path) && file_exists($path) && !is_dir($path);

            // Write image if possible
            if ($canWriteImage) {
                $imageData = base64_encode(file_get_contents($path));
                $staffSignatureSize = hooks()->apply_filters('customer_staff_signature_size', 0);
                $signature .=  '<br/><img src="@' . $imageData . '" width="' . $staffSignatureSize . '"/ />';
            }

            // Write empty line for putting signature manaually if no image exist for the signature
            if (!$canWriteImage) {
                $blankSignatureLine = hooks()->apply_filters('blank_signature_line', '_________________________');
                $blankSignatureLine =  str_repeat('<br />', hooks()->apply_filters('pdf_signature_break_lines', $default_line_breaks)) . $blankSignatureLine;
                $signature .= '<span style="font-weight:bold;text-align: left;">' . $blankSignatureLine . '</span>';
            }

            $width = ($dimensions['wk'] / 2) + $dimensions['lm'];
            $align = 'J';
            $autopadding = true;
            $ln = 0;
            $leftColumnExist = ($index + 1) % 2 === 0;
            if ($leftColumnExist) {
                $align = 'R';
                $autopadding = false;
                $ln = 1;
            }
            $this->MultiCell($width, 0, '<span style="font-weight:bold;text-align: left;">' . $signature . '</span>', 0, $align, 0, $ln, '', '', true, 0, true, $autopadding, 0);
            if ($leftColumnExist) {
                $this->ln($gridGap);
            }
        }
    }
}
