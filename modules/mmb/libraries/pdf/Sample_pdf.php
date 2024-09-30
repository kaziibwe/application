<?php

defined('BASEPATH') || exit('No direct script access allowed');

include_once LIBSPATH . 'pdf/App_pdf.php';

class Sample_pdf extends App_pdf
{
    protected $pdfType;

    protected $page_width;
    protected $page_height;

    public function __construct($pdfType, $section, $tag = '')
    {
        parent::__construct();

        $this->pdfType = $pdfType;
        $this->tag = $tag;

        $this->page_width = $this->getPageDimensions()['wk'];
        $this->page_height = $this->getPageDimensions()['hk'];

        $this->SetTitle('Sample PDF');

        $this->SetDisplayMode('default', 'OneColumn');

        if ('cover_page' == $section) {
            $this->getCoverPage();
        }

        if ('header' == $section) {
            $this->sampleHeader();
        }

        if ('footer' == $section) {
            $this->sampleFooter();
        }

        if ('closing_page' == $section) {
            $this->sampleClosingPage();
        }
    }

    public function sampleHeader()
    {
        $header_text = getPdfOptions($this->pdfType, 'header', 'text');

        $pdf_header_image = getPdfOptions($this->pdfType, 'header', 'image');

        $image_url = base_url('uploads/custom_pdf/' . $this->pdfType . '/' . $pdf_header_image);
        $image_html = '<img src="' . $image_url . '">';

        $this->Image($image_url, 0, 0, $this->page_width, 30);
        $this->writeHTMLCell(0, 0, 10, 12, $header_text, 0, 0, 0, true, '', true);

        $this->SetTopMargin(35);
    }

    public function sampleFooter()
    {
        $this->SetAutoPageBreak(false, 0);

        $footer_text = getPdfOptions($this->pdfType, 'footer', 'text');

        $pdf_footer_image = getPdfOptions($this->pdfType, 'footer', 'image');
        $image_url = base_url('uploads/custom_pdf/' . $this->pdfType . '/' . $pdf_footer_image);

        $this->Image($image_url, 0, $this->page_height - 30, $this->page_width, 30);
        $this->writeHTMLCell(0, 0, 10, -12, $footer_text, 0, 0, 0, true, '', true);

        $this->SetFooterMargin(90);
    }

    public function sampleClosingPage()
    {
        $bMargin = $this->getBreakMargin();
        $auto_page_break = $this->getAutoPageBreak();
        $this->SetAutoPageBreak(false, 0);

        $pdf_cover_image = getPdfOptions($this->pdfType, 'closing_page', 'image');
        $close_page_text = getPdfOptions($this->pdfType, 'closing_page', 'text');

        $parsedClosePageText = parsePDFMergeFields($this->pdfType, $close_page_text);

        $align_from_left = getPdfOptions($this->pdfType, 'closing_page', 'align_from_left');
        $align_from_top = getPdfOptions($this->pdfType, 'closing_page', 'align_from_top');
        $img_file = base_url('uploads/custom_pdf/' . $this->pdfType . '/' . $pdf_cover_image);

        $this->Image($img_file, 0, 0, $this->page_width, $this->page_height, '', '', '', false, 300, '', false, false, 0);
        $this->writeHTMLCell(0, 0, $align_from_left, $align_from_top, $parsedClosePageText, 0, 0, 0, true, '', true);

        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        $this->setPageMark();
    }

    public function prepare()
    {
        return $this->build();
    }

    protected function getCoverPage()
    {
        $bMargin = $this->getBreakMargin();
        $auto_page_break = $this->getAutoPageBreak();
        $this->SetAutoPageBreak(false, 0);

        $pdf_cover_image = getPdfOptions($this->pdfType, 'cover_page', 'image');
        $cover_page_text = getPdfOptions($this->pdfType, 'cover_page', 'text');

        $parsedCoverPageText = parsePDFMergeFields($this->pdfType, $cover_page_text);

        $align_from_left = getPdfOptions($this->pdfType, 'cover_page', 'align_from_left');
        $align_from_top = getPdfOptions($this->pdfType, 'cover_page', 'align_from_top');

        $img_file = base_url('uploads/custom_pdf/' . $this->pdfType . '/' . $pdf_cover_image);

        $this->Image($img_file, 0, 0, $this->page_width, $this->page_height, '', '', '', false, 300, '', false, false, 0);
        $this->writeHTMLCell(0, 0, $align_from_left, $align_from_top, $parsedCoverPageText, 0, 0, 0, true, '', true);

        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        $this->setPageMark();
    }

    protected function type()
    {
        return 'sample_pdf';
    }

    protected function file_path()
    {
        $pdfPath = module_views_path(CUSTOM_PDF_MODULE, 'custom_pdf/pdf/samplePdfTemplate.php');

        return $pdfPath;
    }
}
