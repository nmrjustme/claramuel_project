<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    public function index()
    {
        return view('admin.accounting.index');
    }

    public function monthlyIncomeApi()
    {
        $roomIncome    = $this->getMonthlyRoomIncome();
        $dayTourIncome = $this->getMonthlyDayTourIncome();
        $eventIncome   = $this->getMonthlyEventIncome();

        $combined = $this->combineMonthlyIncome($roomIncome, $dayTourIncome, $eventIncome);

        $totalRevenue   = collect($combined)->sum(fn($c) => $c['room'] + $c['daytour'] );
        $averageMonthly = count($combined) ? $totalRevenue / count($combined) : 0;

        $bestMonthRecord = collect($combined)->sortByDesc(fn($c) => $c['room'] + $c['daytour'])->first();
        $bestMonth = [
            'month'  => $bestMonthRecord['month'] ?? 'N/A',
            'income' => ($bestMonthRecord['room'] ?? 0) + ($bestMonthRecord['daytour'] ?? 0),
        ];

        return response()->json([
            'totalRevenue'   => $totalRevenue,
            'averageMonthly' => $averageMonthly,
            'bestMonth'      => $bestMonth,
            'chartData'      => [
                'labels'   => array_column($combined, 'month'),
                'datasets' => [
                    [
                        'label' => 'Room Income',
                        'data' => array_column($combined, 'room'),
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    ],
                    [
                        'label' => 'Day Tour Income',
                        'data' => array_column($combined, 'daytour'),
                        'borderColor' => 'rgba(255, 206, 86, 1)',
                        'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                    ],
                ],
            ]
        ]);
    }

    private function getMonthlyRoomIncome()
    {
        return DB::table('facility_booking_details')
            ->select(
                DB::raw('SUM(total_price) as total_income'),
                DB::raw('YEAR(checkin_date) as year'),
                DB::raw('MONTH(checkin_date) as month'),
                DB::raw("DATE_FORMAT(checkin_date, '%M %Y') as month_year")
            )
            ->groupBy('year', 'month', 'month_year')
            ->orderBy('year')->orderBy('month')
            ->get()
            ->map(fn($r) => ['month' => $r->month_year, 'income' => (float) $r->total_income]);
    }

    private function getMonthlyDayTourIncome()
    {
        return DB::table('day_tour_log_details')
            ->where('reservation_status', 'paid')
            ->select(
                DB::raw('SUM(total_price) as total_income'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw("DATE_FORMAT(created_at, '%M %Y') as month_year")
            )
            ->groupBy('year', 'month', 'month_year')
            ->orderBy('year')->orderBy('month')
            ->get()
            ->map(fn($r) => ['month' => $r->month_year, 'income' => (float) $r->total_income]);
    }

    private function getMonthlyEventIncome()
    {
        return DB::table('event_booking_details')
            ->select(
                DB::raw('SUM(total_cost) as total_income'),
                DB::raw('YEAR(event_date) as year'),
                DB::raw('MONTH(event_date) as month'),
                DB::raw("DATE_FORMAT(event_date, '%M %Y') as month_year")
            )
            ->groupBy('year', 'month', 'month_year')
            ->orderBy('year')->orderBy('month')
            ->get()
            ->map(fn($r) => ['month' => $r->month_year, 'income' => (float) $r->total_income]);
    }

    private function combineMonthlyIncome($roomIncome, $dayTourIncome, $eventIncome)
    {
        $allMonths = collect(array_merge(
            $roomIncome->pluck('month')->toArray(),
            $dayTourIncome->pluck('month')->toArray(),
            $eventIncome->pluck('month')->toArray(),
        ))->unique()->sort();

        $combined = [];
        foreach ($allMonths as $month) {
            $combined[] = [
                'month'   => $month,
                'room'    => (float) ($roomIncome->firstWhere('month', $month)['income'] ?? 0),
                'daytour' => (float) ($dayTourIncome->firstWhere('month', $month)['income'] ?? 0),
            ];
        }
        return $combined;
    }

    public function export()
{
    $roomIncome    = $this->getMonthlyRoomIncome();
    $dayTourIncome = $this->getMonthlyDayTourIncome();
    $eventIncome   = $this->getMonthlyEventIncome();

    $combined = $this->combineMonthlyIncome($roomIncome, $dayTourIncome, $eventIncome);

    $response = new StreamedResponse(function() use ($combined) {
        $handle = fopen('php://output', 'w');
        // Header row
        fputcsv($handle, ['Month', 'Room Income', 'Day Tour Income', 'Total']);
        // Data rows
        foreach ($combined as $row) {
            $total = $row['room'] + $row['daytour'];
            fputcsv($handle, [
                $row['month'],
                $row['room'],
                $row['daytour'],
                $total,
            ]);
        }
        fclose($handle);
    });

    $filename = 'accounting_report_' . now()->format('Y_m_d_His') . '.csv';
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

    return $response;
}
}
