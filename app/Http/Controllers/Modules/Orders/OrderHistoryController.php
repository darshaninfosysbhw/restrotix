<?php

namespace App\Http\Controllers\Modules\Orders;

use App\Http\Controllers\Controller;
use App\Services\Admin\Orders\OrderHistoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    public function index(Request $request, OrderHistoryService $orderHistoryService): View
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $user->loadMissing('tenant.currency');

        $currencySymbol = trim((string) ($user->tenant?->currency?->symbol ?? session('currency_symbol', '₹')));
        $payload = $orderHistoryService->buildPageData((int) $user->tenant_id, $currencySymbol, $request);

        return view('modules.orders.history', array_merge([
            'user' => $user,
        ], $payload));
    }

    public function export(Request $request, OrderHistoryService $orderHistoryService)
    {
        $user = Auth::user();
        abort_unless($user && $user->tenant_id, 403);

        $user->loadMissing('tenant.currency');

        $currencySymbol = trim((string) ($user->tenant?->currency?->symbol ?? session('currency_symbol', '₹')));
        $export = $orderHistoryService->buildExportRows((int) $user->tenant_id, $currencySymbol, $request);
        $rows = $export['rows'] ?? [];
        $filters = $export['filters'] ?? [];
        $format = strtolower(trim((string) $request->query('format', 'csv')));

        return match ($format) {
            'xls', 'excel' => $this->streamExcelExport($rows),
            'pdf' => $this->downloadPdfExport($rows, $filters),
            default => $this->streamCsvExport($rows),
        };
    }

    private function streamCsvExport(array $rows)
    {
        $fileName = 'order-history-' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");

            $this->writeExportHeaderRow($handle);

            foreach ($rows as $row) {
                $this->writeExportDataRow($handle, $row);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function streamExcelExport(array $rows)
    {
        $fileName = 'order-history-' . now()->format('Y-m-d_H-i-s') . '.xls';

        return response()->streamDownload(function () use ($rows) {
            echo '<html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1" cellspacing="0" cellpadding="4">';
            echo '<thead><tr>';

            foreach ($this->exportColumnLabels() as $label) {
                echo '<th>' . e($label) . '</th>';
            }

            echo '</tr></thead><tbody>';

            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($this->buildExportRowValues($row) as $value) {
                    echo '<td>' . e($value) . '</td>';
                }
                echo '</tr>';
            }

            echo '</tbody></table></body></html>';
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    private function downloadPdfExport(array $rows, array $filters = [])
    {
        $fileName = 'order-history-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = Pdf::loadView('modules.orders.exports.history-pdf', [
            'rows' => $rows,
            'filters' => $filters,
            'generatedAt' => now()->format('d M Y, h:i A'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }

    private function writeExportHeaderRow($handle): void
    {
        fputcsv($handle, $this->exportColumnLabels());
    }

    private function writeExportDataRow($handle, array $row): void
    {
        fputcsv($handle, $this->buildExportRowValues($row));
    }

    private function exportColumnLabels(): array
    {
        return [
            'Order No',
            'Table',
            'Customer',
            'Contact',
            'Source',
            'Items',
            'Amount',
            'Status',
            'Paid',
            'Time',
            'Payment Method',
            'Amount Paid',
            'Subtotal',
            'Discount',
            'Service Charge',
            'Tax',
            'Total',
            'Transaction ID',
            'Notes',
        ];
    }

    private function buildExportRowValues(array $row): array
    {
        $detail = $row['detail'] ?? [];

        return [
            $row['order_no'] ?? 'N/A',
            $row['table'] ?? '—',
            $row['customer'] ?? 'Guest',
            $row['contact'] ?? '',
            $row['source'] ?? '—',
            $row['items'] ?? 0,
            $row['amount'] ?? '—',
            $row['status'] ?? '—',
            $row['paid'] ?? '—',
            $row['time'] ?? '—',
            $detail['payment_method'] ?? '—',
            $detail['amount_paid'] ?? '—',
            $detail['subtotal'] ?? '—',
            $detail['discount'] ?? '—',
            $detail['service'] ?? '—',
            $detail['tax'] ?? '—',
            $detail['total'] ?? '—',
            $detail['transaction_id'] ?? '—',
            $detail['note'] ?? '',
        ];
    }
}
