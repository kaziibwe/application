<?php

defined('BASEPATH') || exit('No direct script access allowed');

include_once APPPATH . '/libraries/pdf/App_pdf.php';

class Estimate_pdf extends App_pdf
{
    protected $estimate;

    protected $page_width;
    protected $page_height;

    protected $is_ending_page = false;

    private $estimate_number;

    protected $render_cover_page = false;

    public function __construct($estimate, $tag = '')
    {
        $this->load_language($estimate->clientid);

        $estimate = hooks()->apply_filters('estimate_html_pdf_data', $estimate);
        $GLOBALS['estimate_pdf'] = $estimate;

        parent::__construct();

        $this->tag = $tag;
        $this->estimate = $estimate;
        $this->estimate_number = format_estimate_number($this->estimate->id);

        $this->page_width = $this->getPageDimensions()['wk'];
        $this->page_height = $this->getPageDimensions()['hk'];

        $this->SetTitle($this->estimate_number);

        // Add Cover page
        $this->getCoverPage();
    }

    public function prepare()
    {
        $this->with_number_to_word($this->estimate->clientid);

        $this->set_view_vars([
            'status' => $this->estimate->status,
            'estimate_number' => $this->estimate_number,
            'estimate' => $this->estimate,
        ]);

        return $this->build();
    }

    // Page header
    public function Header()
    {
        if (($this->render_cover_page == false && $this->page > 1) || $this->is_ending_page == false) {
            $header_text = getPdfOptions('estimate', 'header', 'text');

            $pdf_header_image = getPdfOptions('estimate', 'header', 'image');
            $image_url = base_url('uploads/custom_pdf/estimate/' . $pdf_header_image);
            $image_html = '<img src="' . $image_url . '">';

            $this->Image($image_url, 0, 0, $this->getPageDimensions()['wk'], 30);
            $this->writeHTMLCell(0, 0, 10, 12, $header_text, 0, 0, 0, true, '', true);

            $this->SetTopMargin(35);
        }
    }

    // Page footer
    public function Footer()
    {
        if ($this->render_cover_page === false && $this->is_ending_page === false || ($this->page > 1 && $this->is_ending_page === false)) {
            $footer_text = getPdfOptions('estimate', 'footer', 'text');

            $pdf_footer_image = getPdfOptions('estimate', 'footer', 'image');
            $image_url = base_url('uploads/custom_pdf/estimate/' . $pdf_footer_image);

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

        if (!empty(getPdfOptions('estimate', 'closing_page', 'image')) || !empty(getPdfOptions('estimate', 'closing_page', 'text'))) {
            $this->AddPage();
            $this->is_ending_page = true;
            $bMargin = $this->getBreakMargin();
            $auto_page_break = $this->getAutoPageBreak();
            $this->SetAutoPageBreak(false, 0);

            $pdf_cover_image = getPdfOptions('estimate', 'closing_page', 'image');
            $close_page_text = getPdfOptions('estimate', 'closing_page', 'text');

            $parsedClosePageText = parsePDFMergeFields('estimate', $close_page_text, $this->estimate);

            $align_from_left = getPdfOptions('estimate', 'closing_page', 'align_from_left');
            $align_from_top = getPdfOptions('estimate', 'closing_page', 'align_from_top');
            $img_file = base_url('uploads/custom_pdf/estimate/' . $pdf_cover_image);

            $this->Image($img_file, 0, 0, $this->page_width, $this->page_height, '', '', '', false, 300, '', false, false, 0);
            $this->writeHTMLCell(0, 0, $align_from_left, $align_from_top, $parsedClosePageText, 0, 0, 0, true, '', true);

            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            $this->setPageMark();
        }

        TCPDF::Close();
    }

    protected function type()
    {
        return 'estimate';
    }

    // Cover page
    protected function getCoverPage()
    {
        if (!empty(getPdfOptions('estimate', 'cover_page', 'image')) || !empty(getPdfOptions('estimate', 'cover_page', 'text'))) {
            $this->render_cover_page = true;
            $bMargin = $this->getBreakMargin();
            $auto_page_break = $this->getAutoPageBreak();
            $this->SetAutoPageBreak(false, 0);

            $pdf_cover_image = getPdfOptions('estimate', 'cover_page', 'image');
            $cover_page_text = getPdfOptions('estimate', 'cover_page', 'text');

            $parsedCoverPageText = parsePDFMergeFields('estimate', $cover_page_text, $this->estimate);

            $align_from_left = getPdfOptions('estimate', 'cover_page', 'align_from_left');
            $align_from_top = getPdfOptions('estimate', 'cover_page', 'align_from_top');

            $img_file = base_url('uploads/custom_pdf/estimate/' . $pdf_cover_image);

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
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_estimatepdf.php';
        $actualPath = module_views_path(CUSTOM_PDF_MODULE, 'custom_pdf/pdf_template/custom_estimate_pdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
