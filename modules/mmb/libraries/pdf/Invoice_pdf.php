<?php

defined('BASEPATH') || exit('No direct script access allowed');

include_once APPPATH . '/libraries/pdf/App_pdf.php';

class Invoice_pdf extends App_pdf
{
    protected $invoice;
    protected $is_ending_page = false;

    protected $page_width;
    protected $page_height;

    private $invoice_number;

    protected $render_cover_page = false;

    public function __construct($invoice, $tag = '')
    {
        $this->load_language($invoice->clientid);
        $invoice = hooks()->apply_filters('invoice_html_pdf_data', $invoice);
        $GLOBALS['invoice_pdf'] = $invoice;

        parent::__construct();

        if (!class_exists('Invoices_model', false)) {
            $this->ci->load->model('invoices_model');
        }

        $this->tag = $tag;
        $this->invoice = $invoice;
        $this->invoice_number = format_invoice_number($this->invoice->id);

        $this->page_width = $this->getPageDimensions()['wk'];
        $this->page_height = $this->getPageDimensions()['hk'];

        $this->SetTitle($this->invoice_number);

        // Add Cover page
        $this->getCoverPage();
    }

    public function prepare()
    {
        $this->with_number_to_word($this->invoice->clientid);

        $this->set_view_vars([
            'status' => $this->invoice->status,
            'invoice_number' => $this->invoice_number,
            'payment_modes' => $this->get_payment_modes(),
            'invoice' => $this->invoice,
        ]);

        return $this->build();
    }

    // Page header
    public function Header()
    {
        if (($this->render_cover_page == false && $this->page > 1) || $this->is_ending_page == false) {
            $header_text = getPdfOptions('invoice', 'header', 'text');

            $pdf_header_image = getPdfOptions('invoice', 'header', 'image');
            $image_url = base_url('uploads/custom_pdf/invoice/' . $pdf_header_image);

            $this->Image($image_url, 0, 0, $this->getPageDimensions()['wk'], 30);
            $this->writeHTMLCell(0, 0, 10, 12, $header_text, 0, 0, 0, true, '', true);

            $this->SetTopMargin(35);
        }
    }

    // Page footer
    public function Footer()
    {
        if ($this->render_cover_page === false && $this->is_ending_page === false || ($this->page > 1 && $this->is_ending_page === false)) {
            $footer_text = getPdfOptions('invoice', 'footer', 'text');

            $pdf_footer_image = getPdfOptions('invoice', 'footer', 'image');
            $image_url = base_url('uploads/custom_pdf/invoice/' . $pdf_footer_image);

            $this->Image($image_url, 0, $this->page_height - 30, $this->page_width, 30);
            $this->writeHTMLCell(0, 0, 10, -12, $footer_text, 0, 0, 0, true, '', true);

            $this->SetFooterMargin(90);
        }
    }

    // Closing page
    public function Close()
    {
        if (hooks()->apply_filters('process_pdf_signature_on_close', true)) {
            $this->processSignature();
        }

        hooks()->do_action('pdf_close', ['pdf_instance' => $this, 'type' => $this->type()]);

        $this->last_page_flag = true;

        if (!empty(getPdfOptions('invoice', 'closing_page', 'image')) || !empty(getPdfOptions('invoice', 'closing_page', 'text'))) {
            $this->AddPage();
            $this->is_ending_page = true;
            $bMargin = $this->getBreakMargin();
            $auto_page_break = $this->getAutoPageBreak();
            $this->SetAutoPageBreak(false, 0);

            $pdf_cover_image = getPdfOptions('invoice', 'closing_page', 'image');
            $close_page_text = getPdfOptions('invoice', 'closing_page', 'text');

            $parsedClosePageText = parsePDFMergeFields('invoice', $close_page_text, $this->invoice);

            $align_from_left = getPdfOptions('invoice', 'closing_page', 'align_from_left');
            $align_from_top = getPdfOptions('invoice', 'closing_page', 'align_from_top');
            $img_file = base_url('uploads/custom_pdf/invoice/' . $pdf_cover_image);

            $this->Image($img_file, 0, 0, $this->page_width, $this->page_height, '', '', '', false, 300, '', false, false, 0);
            $this->writeHTMLCell(0, 0, $align_from_left, $align_from_top, $parsedClosePageText, 0, 0, 0, true, '', true);

            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            $this->setPageMark();
        }

        TCPDF::Close();
    }

    protected function type()
    {
        return 'invoice';
    }

    // Cover page
    protected function getCoverPage()
    {
        if (!empty(getPdfOptions('invoice', 'cover_page', 'image')) || !empty(getPdfOptions('invoice', 'cover_page', 'text'))) {
            $this->render_cover_page = true;
            $bMargin = $this->getBreakMargin();
            $auto_page_break = $this->getAutoPageBreak();
            $this->SetAutoPageBreak(false, 0);

            $pdf_cover_image = getPdfOptions('invoice', 'cover_page', 'image');
            $cover_page_text = getPdfOptions('invoice', 'cover_page', 'text');

            $parsedCoverPageText = parsePDFMergeFields('invoice', $cover_page_text, $this->invoice);

            $align_from_left = getPdfOptions('invoice', 'cover_page', 'align_from_left');
            $align_from_top = getPdfOptions('invoice', 'cover_page', 'align_from_top');

            $img_file = base_url('uploads/custom_pdf/invoice/' . $pdf_cover_image);

            $this->Image($img_file, 0, 0, $this->page_width, $this->page_height, '', '', '', false, 300, '', false, false, 0);
            $this->writeHTMLCell(0, 0, $align_from_left, $align_from_top, $parsedCoverPageText, 0, 0, 0, true, '', true);

            // restore auto-page-break status
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $this->setPageMark();

            $this->AddPage();
        }
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_invoicepdf.php';
        $actualPath = module_views_path(CUSTOM_PDF_MODULE, 'custom_pdf/pdf_template/custom_invoice_pdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }

    private function get_payment_modes()
    {
        $this->ci->load->model('payment_modes_model');
        $payment_modes = $this->ci->payment_modes_model->get();

        // In case user want to include {invoice_number} or {client_id} in PDF offline mode description
        foreach ($payment_modes as $key => $mode) {
            if (isset($mode['description'])) {
                $payment_modes[$key]['description'] = str_replace('{invoice_number}', $this->invoice_number, $mode['description']);
                $payment_modes[$key]['description'] = str_replace('{client_id}', $this->invoice->clientid, $mode['description']);
            }
        }

        return $payment_modes;
    }
}
