<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountingService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AccountingReportController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        return view('admin.report.index');
    }

    /**
     * API Endpoint for Dashboard Data
     */
    public function monthlyIncomeApi(Request $request)
    {
        $period = $request->query('period', 'monthly');
        $filterValue = $request->query('filter_value');

        // 1. Get Summary Stats
        $data = $this->accountingService->getFinancialSummary($period, $filterValue);
        $summary = $data['summary'];
        $range = $data['date_range'];

        // 2. Get Transaction Logs (for the table)
        $transactions = $this->accountingService->getTransactionHistory($range['from'], $range['to']);

        return response()->json(array_merge($summary, ['transactions' => $transactions]));
    }

    /**
     * Export to CSV
     */
    public function export(Request $request)
    {
        try {
            $period = $request->query('period', 'monthly');
            $filterValue = $request->query('filter_value');

            // Fetch Data via Service
            $data = $this->accountingService->getFinancialSummary($period, $filterValue);
            $summaryData = $data['summary'];
            $range = $data['date_range'];
            $transactions = $this->accountingService->getTransactionHistory($range['from'], $range['to']);

            $filename = 'financial_report_' . now()->format('Y_m_d_His') . '.csv';

            return new StreamedResponse(function () use ($summaryData, $transactions, $period, $filterValue) {
                $handle = fopen('php://output', 'w');
                fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

                // Header
                fputcsv($handle, ['FINANCIAL REPORT']);
                fputcsv($handle, ['Generated', now()->format('F d, Y h:i A')]);
                fputcsv($handle, ['Period', ucfirst($period) . ' (' . ($filterValue ?: 'Current') . ')']);
                fputcsv($handle, []);

                // Executive Summary
                fputcsv($handle, ['EXECUTIVE SUMMARY']);
                fputcsv($handle, ['Total Revenue', number_format($summaryData['totalIncome'], 2)]);
                fputcsv($handle, ['Total Expenses', number_format($summaryData['totalExpense'], 2)]);
                fputcsv($handle, ['Net Profit', number_format($summaryData['netTotal'], 2)]);
                fputcsv($handle, []);

                // Period Breakdown
                fputcsv($handle, ['PERIOD BREAKDOWN']);
                fputcsv($handle, ['Period', 'Room Rev', 'Day Tour Rev', 'Total Rev', 'Expenses', 'Net Profit']);
                foreach ($summaryData['combined'] as $row) {
                    fputcsv($handle, [
                        $row['label'],
                        number_format($row['room'], 2),
                        number_format($row['daytour'], 2),
                        number_format($row['income'], 2),
                        number_format($row['expense'], 2),
                        number_format($row['net'], 2)
                    ]);
                }
                fputcsv($handle, []);

                // Transaction Log
                fputcsv($handle, ['DETAILED TRANSACTION LOG']);
                fputcsv($handle, ['Date', 'Type', 'Reference', 'Customer', 'Description', 'Method', 'Amount', 'Status']);
                foreach ($transactions as $txn) {
                    fputcsv($handle, [
                        $txn['date'], $txn['type'], $txn['reference'], $txn['customer'], 
                        $txn['description'], $txn['method'], number_format($txn['amount'], 2), $txn['status']
                    ]);
                }
                fclose($handle);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error("Export CSV Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Export failed.');
        }
    }

    /**
     * Export to PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            $period = $request->query('period', 'monthly');
            $filterValue = $request->query('filter_value');

            // Fetch Data via Service
            $data = $this->accountingService->getFinancialSummary($period, $filterValue);
            $range = $data['date_range'];
            $transactions = $this->accountingService->getTransactionHistory($range['from'], $range['to']);

            $pdf = Pdf::loadView('admin.accounting.report_pdf', [
                'summary' => $data['summary'],
                'transactions' => $transactions,
                'period' => $period,
                'filterValue' => $filterValue,
                'generatedAt' => now()
            ])->setPaper('a4', 'landscape');

            return $pdf->download('financial_report.pdf');

        } catch (\Exception $e) {
            Log::error("Export PDF Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'PDF Generation failed.');
        }
    }
}