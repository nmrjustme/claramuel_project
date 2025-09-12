<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function index()
    {
        return view('admin.accounting.index');
    }

    public function monthlyIncomeApi()
    {
        $monthlyIncome = $this->getMonthlyIncomeData();

        // Check if we have data
        if ($monthlyIncome->isEmpty()) {
            return response()->json([
                'totalRevenue' => 0,
                'averageMonthly' => 0,
                'bestMonth' => [
                    'income' => 0,
                    'month' => 'No data available'
                ],
                'chartData' => [
                    'labels' => [],
                    'datasets' => [
                        [
                            'label' => 'Monthly Income',
                            'data' => [],
                            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                            'borderColor' => 'rgba(54, 162, 235, 1)',
                            'borderWidth' => 2,
                            'tension' => 0.3,
                            'fill' => true
                        ]
                    ]
                ]
            ]);
        }

        // Calculate summary statistics
        $totalRevenue = $monthlyIncome->sum('total_income');
        $averageMonthly = $monthlyIncome->avg('total_income');

        // Find the best month
        $bestMonthRecord = $monthlyIncome->sortByDesc('total_income')->first();
        $bestMonth = [
            'income' => $bestMonthRecord->total_income,
            'month' => $bestMonthRecord->month_year
        ];

        // Prepare chart data
        $chartData = $this->prepareIncomeChartData($monthlyIncome);

        return response()->json([
            'totalRevenue' => $totalRevenue,
            'averageMonthly' => $averageMonthly,
            'bestMonth' => $bestMonth,
            'chartData' => $chartData
        ]);
    }

    private function getMonthlyIncomeData()
    {
        return Payments::select(
            DB::raw('SUM(COALESCE(amount, 0)) + SUM(COALESCE(amount_paid, 0)) as total_income'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw("DATE_FORMAT(created_at, '%M %Y') as month_year")
        )
            ->groupBy('year', 'month', 'month_year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }
    
    private function prepareIncomeChartData($monthlyIncome)
    {
        $labels = [];
        $data = [];

        // Reverse to show chronological order
        $reversedData = $monthlyIncome->reverse();

        foreach ($reversedData as $income) {
            $labels[] = $income->month_year;
            $data[] = (float) $income->total_income;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Monthly Income',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => true
                ]
            ]
        ];
    }
}
