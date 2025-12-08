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
        // TRIGGER 1: after_event_insert
        // ============================================
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_event_insert;
            
            CREATE TRIGGER after_event_insert
            AFTER INSERT ON events
            FOR EACH ROW
            BEGIN
                INSERT INTO logs_event (
                    event_id, 
                    action, 
                    new_data, 
                    created_at
                )
                VALUES (
                    NEW.id,
                    'created',
                    JSON_OBJECT(
                        'title', NEW.title,
                        'price', NEW.price,
                        'quota', NEW.quota,
                        'quota_remaining', NEW.quota_remaining,
                        'status', NEW.status,
                        'event_date', NEW.event_date
                    ),
                    NOW()
                );
            END
        ");

        // ============================================
        // TRIGGER 2: after_event_update
        // ============================================
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_event_update;
            
            CREATE TRIGGER after_event_update
            AFTER UPDATE ON events
            FOR EACH ROW
            BEGIN
                INSERT INTO logs_event (
                    event_id, 
                    action, 
                    old_data,
                    new_data, 
                    created_at
                )
                VALUES (
                    NEW.id,
                    'updated',
                    JSON_OBJECT(
                        'title', OLD.title,
                        'price', OLD.price,
                        'quota', OLD.quota,
                        'quota_remaining', OLD.quota_remaining,
                        'status', OLD.status,
                        'event_date', OLD.event_date
                    ),
                    JSON_OBJECT(
                        'title', NEW.title,
                        'price', NEW.price,
                        'quota', NEW.quota,
                        'quota_remaining', NEW.quota_remaining,
                        'status', NEW.status,
                        'event_date', NEW.event_date
                    ),
                    NOW()
                );
            END
        ");

        // ============================================
        // TRIGGER 3: after_transaction_insert
        // ============================================
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_transaction_insert;
            
            CREATE TRIGGER after_transaction_insert
            AFTER INSERT ON transactions
            FOR EACH ROW
            BEGIN
                INSERT INTO logs_transaction (
                    transaction_id,
                    action,
                    new_status,
                    created_at
                )
                VALUES (
                    NEW.id,
                    'created',
                    NEW.status,
                    NOW()
                );
            END
        ");

        // ============================================
        // TRIGGER 4: after_transaction_canceled
        // ============================================
        DB::unprepared("
            DROP TRIGGER IF EXISTS after_transaction_canceled;
            
            CREATE TRIGGER after_transaction_canceled
            AFTER UPDATE ON transactions
            FOR EACH ROW
            BEGIN
                IF OLD.status != 'canceled' AND NEW.status = 'canceled' THEN
                    UPDATE events e
                    INNER JOIN transaction_items ti ON ti.event_id = e.id
                    SET e.quota_remaining = e.quota_remaining + ti.quantity,
                        e.updated_at = NOW()
                    WHERE ti.transaction_id = NEW.id;
                END IF;
            END
        ");

        // ============================================
        // TRIGGER 5: before_ticket_use
        // ============================================
        DB::unprepared("
            DROP TRIGGER IF EXISTS before_ticket_use;
            
            CREATE TRIGGER before_ticket_use
            BEFORE UPDATE ON tickets
            FOR EACH ROW
            BEGIN
                IF OLD.is_used = 0 AND NEW.is_used = 1 THEN
                    SET NEW.used_at = NOW();
                END IF;
                
                IF OLD.is_used = 1 AND NEW.is_used = 1 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Ticket already scanned and cannot be scanned again';
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_event_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_event_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_transaction_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS after_transaction_canceled');
        DB::unprepared('DROP TRIGGER IF EXISTS before_ticket_use');
    }
};
