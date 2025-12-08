<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ============================================
        // VIEW 1: v_monthly_sales
        // ============================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_monthly_sales AS
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m-01') AS month_start,
                DATE_FORMAT(created_at, '%Y-%m') AS month_label,
                SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) AS total_revenue,
                COUNT(CASE WHEN status = 'paid' THEN 1 END) AS total_paid_transactions,
                SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) AS paid_count,
                SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) AS canceled_count,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
                SUM(SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END))
                    OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m-01')) AS running_revenue
            FROM transactions
            GROUP BY DATE_FORMAT(created_at, '%Y-%m-01'), DATE_FORMAT(created_at, '%Y-%m')
        ");

        // ============================================
        // VIEW 2: v_sales_detail
        // ============================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_sales_detail AS
            SELECT 
                t.id AS transaction_id,
                t.transaction_code,
                t.created_at AS transaction_date,
                t.status AS transaction_status,
                t.total_amount,
                u.id AS user_id,
                u.name AS customer_name,
                u.email AS customer_email,
                e.id AS event_id,
                e.title AS event_title,
                e.event_date,
                e.location AS event_location,
                ti.quantity,
                ti.price AS ticket_price,
                ti.subtotal,
                COUNT(tk.id) AS tickets_generated
            FROM transactions t
            JOIN users u ON t.user_id = u.id
            JOIN transaction_items ti ON ti.transaction_id = t.id
            JOIN events e ON ti.event_id = e.id
            LEFT JOIN tickets tk ON tk.transaction_id = t.id AND tk.event_id = e.id
            WHERE t.status = 'paid'
            GROUP BY 
                t.id, t.transaction_code, t.created_at, t.status, t.total_amount,
                u.id, u.name, u.email,
                e.id, e.title, e.event_date, e.location,
                ti.quantity, ti.price, ti.subtotal
            ORDER BY t.created_at DESC
        ");

        // ============================================
        // VIEW 3: v_event_analytics
        // ============================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_event_analytics AS
            SELECT 
                e.id,
                e.title,
                e.description,
                e.location,
                e.event_date,
                e.price,
                e.quota,
                e.quota_remaining,
                e.status,
                COALESCE(SUM(CASE WHEN t.status = 'paid' THEN ti.quantity ELSE 0 END), 0) AS tickets_sold,
                COALESCE(SUM(CASE WHEN t.status = 'paid' THEN ti.subtotal ELSE 0 END), 0) AS total_revenue,
                COUNT(DISTINCT CASE WHEN t.status = 'paid' THEN t.id END) AS total_transactions,
                COUNT(DISTINCT CASE WHEN t.status = 'paid' THEN t.user_id END) AS unique_customers,
                ROUND((COALESCE(SUM(CASE WHEN t.status = 'paid' THEN ti.quantity ELSE 0 END), 0) / e.quota) * 100, 2) AS sold_percentage
            FROM events e
            LEFT JOIN transaction_items ti ON ti.event_id = e.id
            LEFT JOIN transactions t ON t.id = ti.transaction_id
            GROUP BY 
                e.id, e.title, e.description, e.location, e.event_date, 
                e.price, e.quota, e.quota_remaining, e.status
        ");

        // ============================================
        // VIEW 4: v_event_pairs
        // ============================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_event_pairs AS
            SELECT 
                ti1.event_id AS event_id,
                ti2.event_id AS other_event_id,
                COUNT(*) AS times_bought_together
            FROM transaction_items ti1
            JOIN transaction_items ti2 
                ON ti1.transaction_id = ti2.transaction_id
                AND ti1.event_id <> ti2.event_id
            JOIN transactions t ON t.id = ti1.transaction_id
            WHERE t.status = 'paid'
            GROUP BY ti1.event_id, ti2.event_id
            HAVING COUNT(*) >= 1
        ");

        // ============================================
        // VIEW 5: v_top_events
        // ============================================
        DB::unprepared("
            CREATE OR REPLACE VIEW v_top_events AS
            SELECT 
                e.id,
                e.title,
                e.location,
                e.event_date,
                e.price,
                e.quota,
                sales.tickets_sold,
                sales.total_revenue,
                sales.total_transactions,
                RANK() OVER (ORDER BY sales.tickets_sold DESC) AS sales_rank,
                ROUND((sales.tickets_sold / e.quota) * 100, 2) AS sold_percentage
            FROM events e
            INNER JOIN (
                SELECT 
                    ti.event_id,
                    SUM(ti.quantity) AS tickets_sold,
                    SUM(ti.subtotal) AS total_revenue,
                    COUNT(DISTINCT t.id) AS total_transactions
                FROM transaction_items ti
                INNER JOIN transactions t ON t.id = ti.transaction_id
                WHERE t.status = 'paid'
                GROUP BY ti.event_id
            ) AS sales ON sales.event_id = e.id
            WHERE e.status = 'published'
            ORDER BY sales.tickets_sold DESC
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_monthly_sales');
        DB::unprepared('DROP VIEW IF EXISTS v_sales_detail');
        DB::unprepared('DROP VIEW IF EXISTS v_event_analytics');
        DB::unprepared('DROP VIEW IF EXISTS v_event_pairs');
        DB::unprepared('DROP VIEW IF EXISTS v_top_events');
    }
};
