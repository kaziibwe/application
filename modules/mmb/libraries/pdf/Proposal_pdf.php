<?php

defined('BASEPATH') || exit('No direct script access allowed');

include_once APPPATH . '/libraries/pdf/App_pdf.php';

class Proposal_pdf extends App_pdf
{
    protected $proposal;
    protected $is_ending_page = false;

    protected $page_width;
    protected $page_height;

    private $proposal_number;

    protected $render_cover_page = false;

    public function __construct($proposal, $tag = '')
    {
        if (null != $proposal->rel_id && 'customer' == $proposal->rel_type) {
            $this->load_language($proposal->rel_id);
        } elseif (null != $proposal->rel_id && 'lead' == $proposal->rel_type) {
            $CI = &get_instance();

            $CI->db->select('default_language')->where('id', $proposal->rel_id);
            $language = $CI->db->get('leads')->row()->default_language;

            load_pdf_language($language);
        }

        $proposal = hooks()->apply_filters('proposal_html_pdf_data', $proposal);
        $GLOBALS['proposal_pdf'] = $proposal;

        parent::__construct();

        $this->tag = $tag;
        $this->proposal = $proposal;

        $this->proposal_number = format_proposal_number($this->proposal->id);

        $this->page_width = $this->getPageDimensions()['wk'];
        $this->page_height = $this->getPageDimensions()['hk'];

        $this->SetTitle($this->proposal_number);
        $this->SetDisplayMode('default', 'OneColumn');

        # Don't remove these lines - important for the PDF layout
        $this->proposal->content = $this->fix_editor_html($this->proposal->content);

        // Add Cover page
        $this->getCoverPage();
    }

    public function prepare()
    {
        $number_word_lang_rel_id = 'unknown';

        if ('customer' == $this->proposal->rel_type) {
            $number_word_lang_rel_id = $this->proposal->rel_id;
        }

        $this->with_number_to_word($number_word_lang_rel_id);

        $total = '';
        if (0 != $this->proposal->total) {
            $total = app_format_money($this->proposal->total, get_currency($this->proposal->currency));
            $total = _l('proposal_total') . ': ' . $total;
        }

        $this->set_view_vars([
            'number' => $this->proposal_number,
            'proposal' => $this->proposal,
            'total' => $total,
            'proposal_url' => site_url('proposal/' . $this->proposal->id . '/' . $this->proposal->hash),
        ]);

        return $this->build();
    }

    // Page header
    public function Header()
    {
        if (($this->render_cover_page == false && $this->page > 1) || $this->is_ending_page == false) {
            $header_text = getPdfOptions('proposals', 'header', 'text');

            $pdf_header_image = getPdfOptions('proposals', 'header', 'image');
            $image_url = base_url('uploads/custom_pdf/proposals/' . $pdf_header_image);
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
            $footer_text = getPdfOptions('proposals', 'footer', 'text');

            $pdf_footer_image = getPdfOptions('proposals', 'footer', 'image');
            $image_url = base_url('uploads/custom_pdf/proposals/' . $pdf_footer_image);

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

        if (!empty(getPdfOptions('proposals', 'closing_page', 'image')) || !empty(getPdfOptions('proposals', 'closing_page', 'text'))) {
            $this->AddPage();
            $this->is_ending_page = true;
            $bMargin = $this->getBreakMargin();
            $auto_page_break = $this->getAutoPageBreak();
            $this->SetAutoPageBreak(false, 0);

            $pdf_cover_image = getPdfOptions('proposals', 'closing_page', 'image');
            $close_page_text = getPdfOptions('proposals', 'closing_page', 'text');

            $parsedClosePageText = parsePDFMergeFields('proposals', $close_page_text, $this->proposal);

            $align_from_left = getPdfOptions('proposals', 'closing_page', 'align_from_left');
            $align_from_top = getPdfOptions('proposals', 'closing_page', 'align_from_top');
            $img_file = base_url('uploads/custom_pdf/proposals/' . $pdf_cover_image);

            $this->Image($img_file, 0, 0, $this->page_width, $this->page_height, '', '', '', false, 300, '', false, false, 0);
            $this->writeHTMLCell(0, 0, $align_from_left, $align_from_top, $parsedClosePageText, 0, 0, 0, true, '', true);

            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            $this->setPageMark();
        }

        TCPDF::Close();
    }

    protected function type()
    {
        return 'proposal';
    }

    // Cover page
    protected function getCoverPage()
    {
        if (!empty(getPdfOptions('proposals', 'cover_page', 'image')) || !empty(getPdfOptions('proposals', 'cover_page', 'text'))) {
            $this->render_cover_page = true;
            $bMargin = $this->getBreakMargin();
            $auto_page_break = $this->getAutoPageBreak();
            $this->SetAutoPageBreak(false, 0);

            $pdf_cover_image = getPdfOptions('proposals', 'cover_page', 'image');
            $cover_page_text = getPdfOptions('proposals', 'cover_page', 'text');

            $parsedCoverPageText = parsePDFMergeFields('proposals', $cover_page_text, $this->proposal);

            $align_from_left = getPdfOptions('proposals', 'cover_page', 'align_from_left');
            $align_from_top = getPdfOptions('proposals', 'cover_page', 'align_from_top');

            $img_file = base_url('uploads/custom_pdf/proposals/' . $pdf_cover_image);

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
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_proposalpdf.php';
        $actualPath = module_views_path(CUSTOM_PDF_MODULE, 'custom_pdf/pdf_template/custom_proposal_pdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
