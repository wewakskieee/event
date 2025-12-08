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
        // FUNCTION: f_total_ticket_sold
        // ============================================
        DB::unprepared("
            DROP FUNCTION IF EXISTS f_total_ticket_sold;
            
            CREATE FUNCTION f_total_ticket_sold(eventId BIGINT)
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE total INT;
                
                SELECT COALESCE(SUM(ti.quantity), 0)
                INTO total
                FROM transaction_items ti
                INNER JOIN transactions t ON t.id = ti.transaction_id
                WHERE ti.event_id = eventId
                  AND t.status = 'paid';
                
                RETURN total;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS f_total_ticket_sold');
    }
};
