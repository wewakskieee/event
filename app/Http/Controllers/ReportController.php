<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesDetail(Request $request)
    {
        $query = DB::table('v_sales_detail');
        
        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }
        
        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }
        
        $salesDetail = $query->paginate(20);
        
        // Summary statistics
        $summary = DB::table('v_sales_detail')
            ->selectRaw('
                COUNT(DISTINCT transaction_id) as total_transactions,
                COUNT(DISTINCT user_id) as total_customers,
                SUM(quantity) as total_tickets_sold,
                SUM(total_amount) as total_revenue
            ')
            ->first();
        
        $events = DB::table('events')
            ->where('status', 'published')
            ->orderBy('title')
            ->get(['id', 'title']);
        
        return view('admin.reports.sales-detail', compact('salesDetail', 'summary', 'events'));
    }


    public function eventAnalytics(Request $request)
    {
        $query = DB::table('v_event_analytics');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Sort
        $sortBy = $request->get('sort', 'tickets_sold');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $events = $query->get();
        
        // Overall summary
        $summary = DB::table('v_event_analytics')
            ->selectRaw('
                COUNT(*) as total_events,
                SUM(tickets_sold) as total_tickets_sold,
                SUM(total_revenue) as total_revenue,
                AVG(sold_percentage) as avg_sold_percentage
            ')
            ->first();
        
        return view('admin.reports.event-analytics', compact('events', 'summary'));
    }


    public function topEvents(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $topEvents = DB::table('v_top_events')
            ->limit($limit)
            ->get();
        
        return view('admin.reports.top-events', compact('topEvents'));
    }
}
