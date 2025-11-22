<?php

namespace App\Http\Controllers\Internal;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\RentalOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $year = (int) $request->query('year', Carbon::now()->year);
        $month = (int) $request->query('month', Carbon::now()->month); // optional - used for daily view

        // Load all payments related to RentalOrder for the selected year
        $payments = Payment::where('payable_type', RentalOrder::class)
            ->whereYear('created_at', $year)
            ->with('payable.vehicle')
            ->get();

        // Total revenue for selected year
        $totalRevenue = (float) $payments->sum('total_amount');

        // Monthly revenue (1..12)
        $monthlyGrouped = $payments->groupBy(function ($p) {
            return (int) $p->created_at->format('n'); // month number
        });


        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyLabels[] = Carbon::createFromDate($year, $m, 1)->format('F');

            if($monthlyGrouped->has($m)) {

                  $monthlyCounts[] = (float) round($monthlyGrouped[$m]->sum('total_amount'), 2);
            } else {
                $monthlyCounts[] = 0;
            }

        }




        // Daily revenue for selected month
        $dailyPayments = $payments->filter(function ($p) use ($month) {
            return (int) $p->created_at->format('n') === $month;
        })->groupBy(function ($p) {
            return (int) $p->created_at->format('j'); // day of month
        });

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $dailyLabels = [];
        $dailyCounts = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dailyLabels[] = "Day {$d}";
            if($dailyPayments->has($d)) {
                  $dailyCounts[] = (float) round($dailyPayments[$d]->sum('total_amount'), 2);
            } else {
                $dailyCounts[] = 0;
            }
        }

        // Revenue by vehicle type (uses RentalOrder.payments via Payment->payable)
        $orders = RentalOrder::whereYear('rental_date', $year)
            ->with(['vehicle', 'payments'])
            ->get();

        $byType = $orders->groupBy(function ($o) {
            return $o->vehicle?->type ?? 'Unknown';
        })->map(function ($group) {
            return $group->flatMap->payments->sum('total_amount');
        });

        $typeLabels = $byType->keys()->map(fn($k) => $k)->toArray();
        $typeValues = $byType->values()->map(fn($v) => (float) round($v, 2))->toArray();

        // Top performing vehicles by revenue (top 5) for selected year
        $byVehicle = $orders->groupBy('vehicle_id')->map(function ($group) {
            $vehicle = $group->first()->vehicle;
            $revenue = $group->flatMap->payments->sum('total_amount');
            return [
                'vehicle_id' => $vehicle?->id,
                'model' => $vehicle ? ($vehicle->brand . ' ' . $vehicle->model) : 'Unknown',
                'revenue' => (float) round($revenue, 2),
                'rentals' => $group->count()
            ];
        })->values()->sortByDesc('revenue')->take(5)->values()->toArray();

        // Metrics
        $daysWithRevenue = $payments->groupBy(function ($p) {
            return $p->created_at->toDateString();
        })->count();

        $averageDaily = $daysWithRevenue ? round($totalRevenue / $daysWithRevenue, 2) : 0;
        $monthlyGrowth = 0; // could compute by comparing previous months - kept 0 for now
        $yearlyProjection = round($totalRevenue, 2); // simple projection = actual

        $analytics = [
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'metrics' => [
                'totalRevenue' => (float) round($totalRevenue, 2),
                'averageDaily' => $averageDaily,
                'monthlyGrowth' => $monthlyGrowth,
                'yearlyProjection' => $yearlyProjection,
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyCounts,
            ],
            'daily' => [
                'labels' => $dailyLabels,
                'data' => $dailyCounts,
            ],
            'byType' => [
                'labels' => $typeLabels,
                'data' => $typeValues,
            ],
            'topPerformers' => $byVehicle,
        ];

        return Inertia::render('Internal/Report/RevenueReport', [
            'revenue' => $analytics
        ]);
    }

    public function rentalAnalytic(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year) ?? Carbon::now()->year;

        // Total rentals (all time)
        $totalRentals = RentalOrder::whereYear('rental_date', $year)->count();

        // Average rental duration in days (use return_date if exists, otherwise use now)
        $avgDays = RentalOrder::whereYear('rental_date', $year)->get()->avg(function ($order) {
            // 2. Perform calculation in PHP
            $end = $order->return_date ? Carbon::parse($order->return_date) : now();
            $start = Carbon::parse($order->rental_date);

            return $end->diffInDays($start);
        });


        $averageRentalDuration = round($avgDurationRow->avg_days ?? 0, 2);

        // Rental frequency by vehicle type (join vehicles table)
        $frequency = DB::table('rental_orders')
            ->join('vehicles', 'rental_orders.vehicle_id', '=', 'vehicles.id')
            ->select('vehicles.type', DB::raw('COUNT(*) as count'))
            ->whereYear('rental_orders.rental_date', $year)
            ->groupBy('vehicles.type')
            ->orderByDesc('count')
            ->get();

        $rentalFrequencyLabels = $frequency->pluck('type')->map(fn($t) => $t ?? 'Unknown')->toArray();
        $rentalFrequencyCounts = $frequency->pluck('count')->toArray();

        // Monthly rental trends for current year
        $monthly = RentalOrder::whereYear('rental_date', $year)
            ->get() // Fetch the records first
            ->groupBy(function ($data) {
                // Group by month integer (1-12) to match MySQL's behavior
                return  Carbon::parse($data->rental_date)->format('F');
            })
            ->map(function ($group) {
                // Count the items in each group
                return $group->count();
            });



        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyLabels[] = Carbon::createFromDate($year, $m, 1)->format('M');
            $monthlyCounts[] = (int)($monthly[$m]->count ?? 0);
        }

        // Daily rental distribution (Monday..Sunday)
        $days = RentalOrder::whereYear('rental_date', $year)->get() // Fetch data
            ->groupBy(function ($order) {
                // Returns integer: 0 (Sunday) through 6 (Saturday)
                return  Carbon::parse($order->rental_date)->dayOfWeek;
            })
            ->map(function ($group) {
                return $group->count();
            });




        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dailyCounts = [];
        foreach (range(0, 6) as $i) {
            // WEEKDAY() returns 0 (Monday) .. 6 (Sunday)
            $dailyCounts[] = (int)($days[$i] ?? 0);
        }


        $topVehiclesQ = DB::table('rental_orders')
            ->select('vehicle_id', DB::raw('COUNT(*) as rentals'))
            ->groupBy('vehicle_id')
            ->orderByDesc('rentals')
            ->limit(5)
            ->get();

        $topVehicleIds = $topVehiclesQ->pluck('vehicle_id')->toArray();
        $vehicles = DB::table('vehicles')->whereIn('id', $topVehicleIds)->get()->keyBy('id');

        $topVehicles = $topVehiclesQ->map(function ($row) use ($vehicles, $totalRentals) {
            $v = $vehicles[$row->vehicle_id] ?? null;
            $model = $v ? ($v->brand . ' ' . $v->model) : 'Vehicle #' . $row->vehicle_id;
            $util = $totalRentals > 0 ? round(($row->rentals / $totalRentals) * 100, 1) . '%' : '0%';
            return [
                'model' => $model,
                'rentals' => (int)$row->rentals,
                'utilization' => $util,
            ];
        })->toArray();

        // Low performing vehicles (bottom 3)
        $lowQ = DB::table('rental_orders')
            ->select('vehicle_id', DB::raw('COUNT(*) as rentals'))
            ->groupBy('vehicle_id')
            ->orderBy('rentals')
            ->whereYear('rental_date', $year)
            ->limit(3)
            ->get();

        $lowIds = $lowQ->pluck('vehicle_id')->toArray();
        $lowVehiclesData = DB::table('vehicles')
        ->whereIn('id', $lowIds)->get()->keyBy('id');

        $lowPerformingVehicles = $lowQ->map(function ($row) use ($lowVehiclesData, $totalRentals) {
            $v = $lowVehiclesData[$row->vehicle_id] ?? null;
            $model = $v ? ($v->brand . ' ' . $v->model) : 'Vehicle #' . $row->vehicle_id;
            $util = $totalRentals > 0 ? round(($row->rentals / $totalRentals) * 100, 1) . '%' : '0%';
            return [
                'model' => $model,
                'rentals' => (int)$row->rentals,
                'utilization' => $util,
            ];
        })->toArray();

        // Peak utilization (top vehicle utilization)
        $peakUtilization = count($topVehicles) ? $topVehicles[0]['utilization'] : '0%';

        // Most popular day(s)
        $maxDayCount = max($dailyCounts) ?: 0;
        $popularDays = [];
        foreach ($dailyCounts as $idx => $c) {
            if ($c === $maxDayCount && $c > 0) {
                $popularDays[] = $dayNames[$idx];
            }
        }
        $popularDaysLabel = $popularDays ? implode(', ', $popularDays) : 'N/A';

        // Assemble analytics payload
        $analytics = [
            'metrics' => [
                'totalRentals' => (int)$totalRentals,
                'averageRentalDuration' => $averageRentalDuration,
                'peakUtilization' => $peakUtilization,
                'popularDays' => $popularDaysLabel,
            ],
            'rentalFrequency' => [
                'labels' => $rentalFrequencyLabels,
                'data' => $rentalFrequencyCounts
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyCounts
            ],
            'daily' => [
                'labels' => $dayNames,
                'data' => $dailyCounts
            ],
            'topVehicles' => $topVehicles,
            'lowPerformingVehicles' => $lowPerformingVehicles,
            'selectedYear' => $year,
        ];

        return Inertia::render('Internal/Report/RentalAnalytics', [
            'analytics' => $analytics
        ]);
    }

    public function vehicle()
    {
 // load all vehicles
        $vehicles = Vehicle::all();

        // helper: determine an asset/value field on vehicle
        $getValue = function ($vehicle) {
            foreach (['value', 'purchase_cost', 'price', 'msrp'] as $field) {
                if (isset($vehicle->$field) && is_numeric($vehicle->$field)) {
                    return (float) $vehicle->$field;
                }
            }
            return 0.0;
        };

        // totals
        $totalVehicles = $vehicles->count();
        $totalAssetValue = $vehicles->reduce(function ($carry, $v) use ($getValue) {
            return $carry + $getValue($v);
        }, 0.0);

        $averageVehicleValue = $totalVehicles ? round($totalAssetValue / $totalVehicles, 2) : 0.0;

        // maintenance budget estimate (5% of asset value) — adjust if you have real maintenance data
        $maintenanceBudget = round($totalAssetValue * 0.05, 2);

        // Value distribution by type
        $byType = $vehicles->groupBy(function ($v) {
            return $v->type ?? 'Unknown';
        })->map(function ($group) use ($getValue) {
            return (float) round($group->reduce(function ($carry, $v) use ($getValue) {
                return $carry + $getValue($v);
            }, 0.0), 2);
        });

        $typeLabels = $byType->keys()->toArray();
        $typeValues = $byType->values()->toArray();

        // Age distribution buckets (try to use 'year' field if present)
        $ageBuckets = [
            '0-1 year' => 0,
            '1-2 years' => 0,
            '2-3 years' => 0,
            '3-4 years' => 0,
            '4-5 years' => 0,
            '5+ years' => 0,
        ];
        $now = Carbon::now();
        foreach ($vehicles as $v) {
            $year = $v->year ?? $v->model_year ?? null;
            if (!$year) {
                // unknown -> classify as 5+
                $ageBuckets['5+ years']++;
                continue;
            }
            $age = $now->year - (int) $year;
            if ($age <= 1) $ageBuckets['0-1 year']++;
            else if ($age <= 2) $ageBuckets['1-2 years']++;
            else if ($age <= 3) $ageBuckets['2-3 years']++;
            else if ($age <= 4) $ageBuckets['3-4 years']++;
            else if ($age <= 5) $ageBuckets['4-5 years']++;
            else $ageBuckets['5+ years']++;
        }

        // maintenance cost trends: try to derive from payments that reference vehicles via payable relation
        // payments may be polymorphic; attempt to collect payments that have a related vehicle
        $maintenanceMonthly = array_fill(0, 12, 0.0);
        $payments = Payment::with('payable')->get();
        foreach ($payments as $p) {
            // try to find a vehicle on the payable (e.g. payable is RentalOrder with vehicle relation)
            $payable = $p->payable;
            $vehicle = null;
            if ($payable) {
                if (isset($payable->vehicle) && $payable->vehicle) {
                    $vehicle = $payable->vehicle;
                } elseif ($payable instanceof Vehicle) {
                    $vehicle = $payable;
                }
            }
            if ($vehicle) {
                $m = (int) $p->created_at->format('n') - 1;
                $maintenanceMonthly[$m] += (float) ($p->total_amount ?? 0.0);
            }
        }
        // format months labels
        $months = [];
        for ($i = 1; $i <= 12; $i++) $months[] = Carbon::createFromDate($now->year, $i, 1)->format('M');

        // top most valuable vehicles
        $topVehicles = $vehicles->map(function ($v) use ($getValue) {
            return [
                'model' => trim(($v->brand ?? '') . ' ' . ($v->model ?? '')) ?: 'Unknown',
                'type' => $v->type ?? 'Unknown',
                'value' => $getValue($v),
                'year' => $v->year ?? null,
            ];
        })->sortByDesc('value')->take(5)->values()->toArray();

        $metrics = [
            'totalAssetValue' => (float) round($totalAssetValue, 2),
            'averageVehicleValue' => (float) round($averageVehicleValue, 2),
            'totalVehicles' => $totalVehicles,
            'maintenanceBudget' => (float) round($maintenanceBudget, 2),
        ];

        $vehiclesReport = [
            'metrics' => $metrics,
            'byType' => [
                'labels' => $typeLabels,
                'data' => $typeValues,
            ],
            'ageDistribution' => [
                'labels' => array_keys($ageBuckets),
                'data' => array_values($ageBuckets),
            ],
            'maintenance' => [
                'labels' => $months,
                'data' => array_map(function ($v) { return (float) round($v, 2); }, $maintenanceMonthly),
            ],
            'topVehicles' => $topVehicles,
        ];

        return Inertia::render('Internal/Report/VehicleReport', [
            'vehiclesReport' => $vehiclesReport,
        ]);
    }
}
