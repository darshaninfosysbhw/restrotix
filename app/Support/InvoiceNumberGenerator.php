<?php

namespace App\Support;

use App\Models\OrderInvoice;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class InvoiceNumberGenerator
{
    /**
     * Generate invoice number branch-wise.
     *
     * Example:
     * F-TI0001-83/84
     * F-TI0002-83/84
     */
    public static function generate(
        int $tenantId,
        int $branchId,
        ?string $tenantName = null,
        ?string $branchName = null,
        ?CarbonInterface $issuedAt = null,
        string $documentType = 'TI'
    ): string {
        $issuedAt = $issuedAt
            ? Carbon::instance($issuedAt)
            : now();

        $documentType = strtoupper(trim($documentType));

        if ($documentType === '') {
            $documentType = 'TI';
        }

        $prefix = self::resolvePrefix(
            $tenantName,
            $branchName
        );

        $fiscalYear = self::resolveFiscalYearLabel(
            $issuedAt
        );

        $sequence = self::nextSequence(
            $tenantId,
            $branchId,
            $prefix,
            $documentType,
            $fiscalYear
        );

        return sprintf(
            '%s-%s%04d-%s',
            $prefix,
            $documentType,
            $sequence,
            $fiscalYear
        );
    }

    /**
     * Resolve invoice prefix.
     */
    private static function resolvePrefix(
        ?string $tenantName,
        ?string $branchName
    ): string {
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

        $clean = preg_replace(
            '/[^A-Za-z0-9\s]+/',
            ' ',
            $source
        ) ?: '';

        $parts = preg_split(
            '/\s+/',
            trim($clean)
        ) ?: [];

        $letters = '';

        foreach ($parts as $part) {
            $part = preg_replace(
                '/[^A-Za-z0-9]+/',
                '',
                $part
            ) ?: '';

            if ($part === '') {
                continue;
            }

            $letters .= strtoupper(
                substr($part, 0, 1)
            );

            if (strlen($letters) >= 2) {
                break;
            }
        }

       
        if ($letters === '') {
            $cleanSource = preg_replace(
                '/[^A-Za-z0-9]+/',
                '',
                $source
            ) ?: '';

            $letters = strtoupper(
                substr($cleanSource, 0, 2)
            );
        }

        return substr($letters, 0, 2);
    }

    private static function resolveFiscalYearLabel(
        CarbonInterface $date
    ): string {
        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $day = (int) $date->format('j');

        /*
         * Nepal fiscal year starts around Shrawan 1
         * (approximately July 17).
         */
        $fiscalStartYear =
            ($month > 7 || ($month === 7 && $day >= 17))
                ? $year + 57
                : $year + 56;

        $fiscalEndYear = $fiscalStartYear + 1;

        return substr(
            (string) $fiscalStartYear,
            -2
        )
            . '/'
            . substr(
                (string) $fiscalEndYear,
                -2
            );
    }

   
    private static function nextSequence(
        int $tenantId,
        int $branchId,
        string $prefix,
        string $documentType,
        string $fiscalYear
    ): int {
        $pattern = sprintf(
            '%s-%s%%-%s',
            $prefix,
            $documentType,
            $fiscalYear
        );

        $invoiceNumbers = OrderInvoice::query()
            ->where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where(
                'invoice_number',
                'like',
                $pattern
            )
            ->pluck('invoice_number');

        if ($invoiceNumbers->isEmpty()) {
            return 1;
        }

        $documentTypePattern = preg_quote(
            $documentType,
            '/'
        );

        $fiscalYearPattern = preg_quote(
            $fiscalYear,
            '/'
        );

        $maxSequence = 0;

        foreach ($invoiceNumbers as $invoiceNumber) {
            if (
                preg_match(
                    '/-' .
                    $documentTypePattern .
                    '(\d+)-' .
                    $fiscalYearPattern .
                    '$/',
                    (string) $invoiceNumber,
                    $matches
                )
            ) {
                $sequence = (int) $matches[1];

                if ($sequence > $maxSequence) {
                    $maxSequence = $sequence;
                }
            }
        }

        return $maxSequence + 1;
    }
}