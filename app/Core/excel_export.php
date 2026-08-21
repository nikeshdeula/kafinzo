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

if (!function_exists('excel_export_styled')) {
    /**
     * Styled Excel export matching the exact format:
     * - Row 1: Company name (yellow, merged, bold, 14pt)
     * - Row 2: Report title (yellow, merged, bold, 12pt)
     * - Row 3: Period (green, merged, bold, 11pt)
     * - Row 4: Column headers (blue bg, white text, bold)
     * - Data rows with left-aligned text, right-aligned numbers
     * - Total row (orange bg, bold)
     */
    function excel_export_styled(
        string $companyName,
        string $reportTitle,
        string $period,
        array $headers,
        array $rows,
        ?array $totals = null,
        string $filename = 'export.xls'
    ): void {
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.str_replace(['"', '\\'], '', $filename).'"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $colCount = count($headers);

        echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        echo '<?mso-application progid="Excel.Sheet"?>'."\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"';
        echo ' xmlns:o="urn:schemas-microsoft-com:office:office"';
        echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"';
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"';
        echo ' xmlns:html="http://www.w3.org/TR/REC-html40">'."\n";

        // Styles
        echo '<Styles>'."\n";
        // Define border style once
        echo '  <Style ss:ID="defaultBorder"><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/></Borders></Style>'."\n";
        echo '  <Style ss:ID="companyHeader" ss:Parent="defaultBorder"><Font ss:Bold="1" ss.Size="14" ss.Color="#000000"/><Interior ss:Color="#FFD700" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>'."\n";
        echo '  <Style ss:ID="reportTitle" ss:Parent="defaultBorder"><Font ss:Bold="1" ss.Size="12" ss.Color="#000000"/><Interior ss:Color="#FFD700" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>'."\n";
        echo '  <Style ss:ID="periodHeader" ss:Parent="defaultBorder"><Font ss:Bold="1" ss.Size="11" ss.Color="#000000"/><Interior ss:Color="#4CAF50" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>'."\n";
        echo '  <Style ss:ID="colHeader" ss:Parent="defaultBorder"><Font ss:Bold="1" ss.Size="10" ss.Color="#FFFFFF"/><Interior ss:Color="#2196F3" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/></Style>'."\n";
        echo '  <Style ss:ID="textLeft" ss:Parent="defaultBorder"><Font ss:Size="10"/><Alignment ss:Horizontal="Left" ss:Vertical="Center"/></Style>'."\n";
        echo '  <Style ss:ID="textRight" ss:Parent="defaultBorder"><Font ss:Size="10"/><Alignment ss:Horizontal="Right" ss:Vertical="Center"/></Style>'."\n";
        echo '  <Style ss:ID="totalLabel" ss:Parent="defaultBorder"><Font ss:Bold="1" ss.Size="10" ss.Color="#000000"/><Interior ss:Color="#FF9800" ss:Pattern="Solid"/><Alignment ss:Horizontal="Right" ss:Vertical="Center"/></Style>'."\n";
        echo '  <Style ss:ID="totalValue" ss:Parent="defaultBorder"><Font ss:Bold="1" ss.Size="10" ss.Color="#000000"/><Interior ss:Color="#FF9800" ss:Pattern="Solid"/><Alignment ss:Horizontal="Right" ss:Vertical="Center"/></Style>'."\n";
        echo '  <Style ss:ID="totalEmpty" ss:Parent="defaultBorder"><Font ss:Bold="1" ss.Size="10"/><Interior ss:Color="#FF9800" ss:Pattern="Solid"/><Alignment ss:Horizontal="Right" ss:Vertical="Center"/></Style>'."\n";
        echo '</Styles>'."\n";

        echo '<Worksheet ss:Name="Sheet1">'."\n";
        echo '<Table>'."\n";

        // Column widths matching the screenshot
        $colWidths = [100, 280, 80, 120, 100, 120, 120, 130, 110];
        for ($i = 0; $i < $colCount; $i++) {
            $w = $colWidths[$i] ?? 120;
            echo '<Column ss:Width="'.$w.'"/>'."\n";
        }

        // Row 1: Company Name (merged)
        echo '<Row ss:Height="30">'."\n";
        echo '<Cell ss:StyleID="companyHeader" ss:MergeAcross="'.($colCount - 1).'"><Data ss:Type="String">'.htmlspecialchars($companyName, ENT_QUOTES).'</Data></Cell>'."\n";
        echo '</Row>'."\n";

        // Row 2: Report Title (merged)
        echo '<Row ss:Height="25">'."\n";
        echo '<Cell ss:StyleID="reportTitle" ss:MergeAcross="'.($colCount - 1).'"><Data ss:Type="String">'.htmlspecialchars($reportTitle, ENT_QUOTES).'</Data></Cell>'."\n";
        echo '</Row>'."\n";

        // Row 3: Period (merged)
        echo '<Row ss:Height="25">'."\n";
        echo '<Cell ss:StyleID="periodHeader" ss:MergeAcross="'.($colCount - 1).'"><Data ss:Type="String">'.htmlspecialchars($period, ENT_QUOTES).'</Data></Cell>'."\n";
        echo '</Row>'."\n";

        // Row 4: Column Headers
        echo '<Row ss:Height="22">'."\n";
        foreach ($headers as $h) {
            echo '<Cell ss:StyleID="colHeader"><Data ss:Type="String">'.htmlspecialchars($h, ENT_QUOTES).'</Data></Cell>'."\n";
        }
        echo '</Row>'."\n";

        // Data Rows - all cells as strings to prevent corruption
        foreach ($rows as $row) {
            echo '<Row>'."\n";
            $colIdx = 0;
            foreach ($row as $key => $val) {
                $v = ($val === null || $val === '') ? '-' : $val;
                $style = $colIdx === 0 ? 'textLeft' : 'textRight';
                echo '<Cell ss:StyleID="'.$style.'"><Data ss:Type="String">'.htmlspecialchars($v, ENT_QUOTES).'</Data></Cell>'."\n";
                $colIdx++;
            }
            echo '</Row>'."\n";
        }

        // Totals Row
        if (!empty($totals)) {
            echo '<Row ss:Height="22">'."\n";
            $colIdx = 0;
            foreach ($totals as $key => $val) {
                $v = ($val === null || $val === '') ? '' : $val;
                if ($colIdx === 0) {
                    $style = 'totalLabel';
                } elseif ($v === '') {
                    $style = 'totalEmpty';
                } else {
                    $style = 'totalValue';
                }
                echo '<Cell ss:StyleID="'.$style.'"><Data ss:Type="String">'.htmlspecialchars($v, ENT_QUOTES).'</Data></Cell>'."\n";
                $colIdx++;
            }
            echo '</Row>'."\n";
        }

        echo '</Table>'."\n";
        echo '</Worksheet>'."\n";
        echo '</Workbook>';
        exit;
    }
}
