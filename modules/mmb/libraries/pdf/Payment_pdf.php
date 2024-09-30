<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . '/libraries/pdf/App_pdf.php';

class Payment_pdf extends App_pdf
{
    protected $payment;
    protected $is_ending_page = false;

    protected $page_width;
    protected $page_height;

    protected $render_cover_page = false;

    public function __construct($payment, $tag = '')
    {
        $GLOBALS['payment_pdf'] = $payment;

        $this->load_language($payment->invoice_data->clientid);

        parent::__construct();

        if (!class_exists('payments_model', false)) {
            $this->ci->load->model('payments_model');
        }

        $this->page_width = $this->getPageDimensions()['wk'];
        $this->page_height = $this->getPageDimensions()['hk'];

        $this->payment = $payment;
        $this->tag = $tag;

        $this->SetTitle(_l('payment') . ' #' . $this->payment->paymentid);

        // Add Cover page
        $this->getCoverPage();
    }

    public function prepare()
    {
        $amountDue = ($this->payment->invoice_data->status != Invoices_model::STATUS_PAID && $this->payment->invoice_data->status != Invoices_model::STATUS_CANCELLED ? true : false);

        $this->set_view_vars([
            'payment' => $this->payment,
            'amountDue' => $amountDue,
        ]);

        return $this->build();
    }

    // Page header
    public function Header()
    {
        if (($this->render_cover_page == false && $this->page > 1) || $this->is_ending_page == false) {
            $header_text = getPdfOptions('payment', 'header', 'text');

            $pdf_header_image = getPdfOptions('payment', 'header', 'image');
            $image_url = base_url('uploads/custom_pdf/payment/' . $pdf_header_image);

            $this->Image($image_url, 0, 0, $this->getPageDimensions()['wk'], 30);
            $this->writeHTMLCell(0, 0, 10, 12, $header_text, 0, 0, 0, true, '', true);

            $this->SetTopMargin(35);
        }
    }

    // Page footer
    public function Footer()
    {
        if ($this->render_cover_page === false && $this->is_ending_page === false || ($this->page > 1 && $this->is_ending_page === false)) {
            $footer_text = getPdfOptions('payment', 'footer', 'text');

            $pdf_footer_image = getPdfOptions('payment', 'footer', 'image');
            $image_url = base_url('uploads/custom_pdf/payment/' . $pdf_footer_image);

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

        if (!empty(getPdfOptions('payment', 'closing_page', 'image')) || !empty(getPdfOptions('payment', 'closing_page', 'text'))) {
            $this->AddPage();
            $this->is_ending_page = true;
            $bMargin = $this->getBreakMargin();
            $auto_page_break = $this->getAutoPageBreak();
            $this->SetAutoPageBreak(false, 0);

            $pdf_cover_image = getPdfOptions('payment', 'closing_page', 'image');
            $close_page_text = getPdfOptions('payment', 'closing_page', 'text');

            $parsedClosePageText = parsePDFMergeFields('payment', $close_page_text, $this->payment);
            $align_from_left = getPdfOptions('payment', 'closing_page', 'align_from_left');
            $align_from_top = getPdfOptions('payment', 'closing_page', 'align_from_top');
            $img_file = base_url('uploads/custom_pdf/payment/' . $pdf_cover_image);

            $this->Image($img_file, 0, 0, $this->page_width, $this->page_height, '', '', '', false, 300, '', false, false, 0);
            $this->writeHTMLCell(0, 0, $align_from_left, $align_from_top, $parsedClosePageText, 0, 0, 0, true, '', true);

            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            $this->setPageMark();
        }

        TCPDF::Close();
    }

    protected function type()
    {
        return 'payment';
    }

    // Cover page
    protected function getCoverPage()
    {
        if (!empty(getPdfOptions('payment', 'cover_page', 'image')) || !empty(getPdfOptions('payment', 'cover_page', 'text'))) {
            $this->render_cover_page = true;
            $bMargin = $this->getBreakMargin();
            $auto_page_break = $this->getAutoPageBreak();
            $this->SetAutoPageBreak(false, 0);

            $pdf_cover_image = getPdfOptions('payment', 'cover_page', 'image');
            $cover_page_text = getPdfOptions('payment', 'cover_page', 'text');

            $parsedCoverPageText = parsePDFMergeFields('payment', $cover_page_text, $this->payment);

            $align_from_left = getPdfOptions('payment', 'cover_page', 'align_from_left');
            $align_from_top = getPdfOptions('payment', 'cover_page', 'align_from_top');

            $img_file = base_url('uploads/custom_pdf/payment/' . $pdf_cover_image);

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
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_paymentpdf.php';
        $actualPath = module_views_path(CUSTOM_PDF_MODULE, 'custom_pdf/pdf_template/custom_payment_pdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
