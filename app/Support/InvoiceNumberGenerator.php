<?php

namespace App\Support;

use App\Models\OrderInvoice;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class InvoiceNumberGenerator
{
    public static function generate(
        int $tenantId,
        ?string $tenantName = null,
        ?string $branchName = null,
        ?CarbonInterface $issuedAt = null,
        string $documentType = 'TI'
    ): string {
        $issuedAt = $issuedAt ? Carbon::instance($issuedAt) : now();
        $documentType = strtoupper(trim($documentType)) ?: 'TI';
        $prefix = self::resolvePrefix($tenantName, $branchName);
        $fiscalYear = self::resolveFiscalYearLabel($issuedAt);
        $sequence = self::nextSequence($tenantId, $prefix, $documentType, $fiscalYear);

        return sprintf('%s-%s%04d-%s', $prefix, $documentType, $sequence, $fiscalYear);
    }

    private static function resolvePrefix(?string $tenantName, ?string $branchName): string
    {
        foreach ([$tenantName, $branchName] as $source) {
            $prefix = self::extractPrefix((string) $source);
            if ($prefix !== '') {
                return $prefix;
            }
        }

        return 'FP';
    }

    private static function extractPrefix(string $source): string
    {
        $source = trim($source);
        if ($source === '') {
            return '';
        }

        $clean = preg_replace('/[^A-Za-z0-9\s]+/', ' ', $source) ?: '';
        $parts = preg_split('/\s+/', trim($clean)) ?: [];

        $letters = '';
        foreach ($parts as $part) {
            $part = preg_replace('/[^A-Za-z0-9]+/', '', $part) ?: '';
            if ($part === '') {
                continue;
            }

            $letters .= strtoupper(substr($part, 0, 1));
            if (strlen($letters) >= 2) {
                break;
            }
        }

        if ($letters === '') {
            $letters = strtoupper(substr(preg_replace('/[^A-Za-z0-9]+/', '', $source) ?: '', 0, 2));
        }

        return substr($letters, 0, 2);
    }

    private static function resolveFiscalYearLabel(CarbonInterface $date): string
    {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $day = (int) $date->format('j');

        // Nepal fiscal year starts around mid-July (Shrawan 1).
        $fiscalStartYear = ($month > 7 || ($month === 7 && $day >= 17))
            ? $year + 57
            : $year + 56;
        $fiscalEndYear = $fiscalStartYear + 1;

        return substr((string) $fiscalStartYear, -2) . '/' . substr((string) $fiscalEndYear, -2);
    }

    private static function nextSequence(int $tenantId, string $prefix, string $documentType, string $fiscalYear): int
    {
        $pattern = $prefix . '-' . $documentType . '%-' . $fiscalYear;

        return ((int) OrderInvoice::query()
            ->where('tenant_id', $tenantId)
            ->where('invoice_number', 'like', $pattern)
            ->count()) + 1;
    }
}
