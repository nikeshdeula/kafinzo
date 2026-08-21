<?php

if (!function_exists('tax_settings')) {
    function tax_settings(): array {
        $bs = new \App\Models\BusinessSetting();
        return [
            'name' => $bs->get('tax_name', 'VAT'),
            'rate' => (float)($bs->get('tax_rate', '13')),
            'pan_format' => $bs->get('pan_format', 'XXXXXXXXX'),
            'vat_format' => $bs->get('vat_format', 'XXXXXXXXX'),
        ];
    }
}

if (!function_exists('tax_rate')) {
    function tax_rate(): float {
        return tax_settings()['rate'];
    }
}

if (!function_exists('tax_name')) {
    function tax_name(): string {
        return tax_settings()['name'];
    }
}

if (!function_exists('calc_tax')) {
    function calc_tax(float $amount, ?float $rate = null): float {
        if ($rate === null) { $rate = tax_rate(); }
        return round($amount * ($rate / 100), 2);
    }
}
