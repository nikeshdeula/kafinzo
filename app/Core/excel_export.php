<?php

if (!function_exists('excel_export')) {
    function excel_export(string $title, array $headers, array $rows, string $filename): void {
        $ts = gmdate('D, d M Y H:i:s');
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.str_replace(['"', '\\'], '', $filename).'"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        echo '<?mso-application progid="Excel.Sheet"?>'."\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        echo ' xmlns:o="urn:schemas-microsoft-com:office:office"';
        echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"';
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"';
        echo ' xmlns:html="http://www.w3.org/TR/REC-html40">'."\n";
        echo '<Worksheet ss:Name="'.htmlspecialchars($title, ENT_QUOTES).'">'."\n";
        echo '<Table>'."\n";

        if (!empty($headers)) {
            echo '<Row>'."\n";
            foreach ($headers as $h) {
                echo '<Cell><Data ss:Type="String">'.htmlspecialchars($h, ENT_QUOTES).'</Data></Cell>'."\n";
            }
            echo '</Row>'."\n";
        }

        foreach ($rows as $row) {
            echo '<Row>'."\n";
            foreach ($row as $cell) {
                $val = $cell === null ? '' : $cell;
                echo '<Cell><Data ss:Type="String">'.htmlspecialchars($val, ENT_QUOTES).'</Data></Cell>'."\n";
            }
            echo '</Row>'."\n";
        }

        echo '</Table>'."\n";
        echo '</Worksheet>'."\n";
        echo '</Workbook>';
        exit;
    }
}
