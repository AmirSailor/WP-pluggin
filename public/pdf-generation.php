<?php

/**
 * This file handles the generation of PDFs for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Generate a PDF of the lecture transcript and summary.
 *
 * This function is hooked into the 'template_redirect' action.
 */
function aigq_generate_pdf() {
    if (isset($_GET['generate_pdf']) && $_GET['generate_pdf'] === 'true') {
        if (is_singular('quiz')) {
            $lecture_id = get_post_meta(get_the_ID(), '_quiz_lecture_id', true);
            $transcript = get_post_meta($lecture_id, '_lecture_transcript', true);
            $quiz_data_json = get_post_meta($lecture_id, '_lecture_quiz_data', true);
            $quiz_data = json_decode($quiz_data_json, true);
            $summary = $quiz_data['summary'];

            // Create the HTML for the PDF
            $html = '<h1>' . get_the_title($lecture_id) . '</h1>';
            $html .= '<h2>' . __('Summary', 'aigq') . '</h2>';
            $html .= '<p>' . esc_html($summary) . '</p>';
            $html .= '<h2>' . __('Transcript', 'aigq') . '</h2>';
            $html .= '<p>' . esc_html($transcript) . '</p>';

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',16);
            $pdf->Cell(40,10,get_the_title($lecture_id));
            $pdf->Ln();
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(40,10,__('Summary', 'aigq'));
            $pdf->Ln();
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,10,esc_html($summary));
            $pdf->Ln();
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(40,10,__('Transcript', 'aigq'));
            $pdf->Ln();
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,10,esc_html($transcript));
            $pdf->Output();
            exit;
        }
    }
}
add_action('template_redirect', 'aigq_generate_pdf');
