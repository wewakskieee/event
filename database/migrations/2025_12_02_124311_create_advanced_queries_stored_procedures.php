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
        // STORED PROCEDURE 1: SP_CreateTransaction
        // ============================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS SP_CreateTransaction;
            
            CREATE PROCEDURE SP_CreateTransaction(
                IN p_event_id BIGINT,
                IN p_user_id BIGINT,
                IN p_qty INT,
                OUT p_transaction_id BIGINT,
                OUT p_message VARCHAR(255)
            )
            BEGIN
                DECLARE v_price DECIMAL(10,2);
                DECLARE v_quota_remaining INT;
                DECLARE v_total DECIMAL(10,2);
                DECLARE v_transaction_code VARCHAR(50);
                DECLARE v_ticket_code VARCHAR(50);
                DECLARE v_qr_code VARCHAR(255);
                DECLARE v_counter INT DEFAULT 1;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_message = 'Transaction failed';
                    SET p_transaction_id = NULL;
                END;
                
                START TRANSACTION;
                
                SELECT price, quota_remaining
                INTO v_price, v_quota_remaining
                FROM events
                WHERE id = p_event_id
                FOR UPDATE;
                
                IF v_quota_remaining < p_qty THEN
                    SET p_message = 'Insufficient quota';
                    SET p_transaction_id = NULL;
                    ROLLBACK;
                ELSE
                    SET v_total = v_price * p_qty;
                    SET v_transaction_code = CONCAT('TRX-', UNIX_TIMESTAMP(), '-', p_user_id);
                    
                    INSERT INTO transactions (user_id, transaction_code, total_amount, status, created_at, updated_at)
                    VALUES (p_user_id, v_transaction_code, v_total, 'pending', NOW(), NOW());
                    
                    SET p_transaction_id = LAST_INSERT_ID();
                    
                    INSERT INTO transaction_items (transaction_id, event_id, quantity, price, subtotal, created_at, updated_at)
                    VALUES (p_transaction_id, p_event_id, p_qty, v_price, v_total, NOW(), NOW());
                    
                    WHILE v_counter <= p_qty DO
                        SET v_ticket_code = CONCAT('TIX-', p_transaction_id, '-', LPAD(v_counter, 3, '0'));
                        SET v_qr_code = MD5(CONCAT(v_ticket_code, p_event_id, p_user_id, NOW()));
                        
                        INSERT INTO tickets (
                            transaction_id, event_id, user_id, 
                            ticket_code, qr_code, is_used, 
                            created_at, updated_at
                        )
                        VALUES (
                            p_transaction_id, p_event_id, p_user_id,
                            v_ticket_code, v_qr_code, 0,
                            NOW(), NOW()
                        );
                        
                        SET v_counter = v_counter + 1;
                    END WHILE;
                    
                    UPDATE events
                    SET quota_remaining = quota_remaining - p_qty,
                        updated_at = NOW()
                    WHERE id = p_event_id;
                    
                    COMMIT;
                    SET p_message = 'Success';
                END IF;
            END
        ");

        // ============================================
        // STORED PROCEDURE 2: SP_ApplyPromoCode
        // ============================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS SP_ApplyPromoCode;
            
            CREATE PROCEDURE SP_ApplyPromoCode(
                IN p_transaction_id BIGINT,
                IN p_promo_code VARCHAR(50),
                IN p_user_id BIGINT,
                OUT p_discount_amount DECIMAL(10,2),
                OUT p_final_amount DECIMAL(10,2),
                OUT p_message VARCHAR(255)
            )
            BEGIN
                DECLARE v_promo_id BIGINT;
                DECLARE v_promo_type VARCHAR(20);
                DECLARE v_promo_value DECIMAL(10,2);
                DECLARE v_max_uses INT;
                DECLARE v_current_uses INT;
                DECLARE v_valid_from DATETIME;
                DECLARE v_valid_until DATETIME;
                DECLARE v_is_active BOOLEAN;
                DECLARE v_total_amount DECIMAL(10,2);
                DECLARE v_user_usage INT;
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_message = 'Failed to apply promo code';
                    SET p_discount_amount = 0;
                    SET p_final_amount = 0;
                END;
                
                START TRANSACTION;
                
                -- Get transaction amount
                SELECT total_amount
                INTO v_total_amount
                FROM transactions
                WHERE id = p_transaction_id AND user_id = p_user_id
                FOR UPDATE;
                
                IF v_total_amount IS NULL THEN
                    SET p_message = 'Transaction not found';
                    SET p_discount_amount = 0;
                    SET p_final_amount = 0;
                    ROLLBACK;
                ELSE
                    -- Get promo code details
                    SELECT 
                        id, type, value, max_uses, uses, 
                        valid_from, valid_until, active
                    INTO 
                        v_promo_id, v_promo_type, v_promo_value, 
                        v_max_uses, v_current_uses,
                        v_valid_from, v_valid_until, v_is_active
                    FROM promo_codes
                    WHERE code = p_promo_code
                    FOR UPDATE;
                    
                    -- Validate promo code
                    IF v_promo_id IS NULL THEN
                        SET p_message = 'Promo code not found';
                        SET p_discount_amount = 0;
                        SET p_final_amount = v_total_amount;
                        ROLLBACK;
                        
                    ELSEIF v_is_active = 0 THEN
                        SET p_message = 'Promo code is inactive';
                        SET p_discount_amount = 0;
                        SET p_final_amount = v_total_amount;
                        ROLLBACK;
                        
                    ELSEIF NOW() < v_valid_from THEN
                        SET p_message = 'Promo code not yet valid';
                        SET p_discount_amount = 0;
                        SET p_final_amount = v_total_amount;
                        ROLLBACK;
                        
                    ELSEIF NOW() > v_valid_until THEN
                        SET p_message = 'Promo code has expired';
                        SET p_discount_amount = 0;
                        SET p_final_amount = v_total_amount;
                        ROLLBACK;
                        
                    ELSEIF v_current_uses >= v_max_uses THEN
                        SET p_message = 'Promo code usage limit reached';
                        SET p_discount_amount = 0;
                        SET p_final_amount = v_total_amount;
                        ROLLBACK;
                    ELSE
                        -- Check user usage
                        SELECT COUNT(*) INTO v_user_usage
                        FROM transactions
                        WHERE user_id = p_user_id 
                          AND promo_code_id = v_promo_id
                          AND status IN ('paid', 'pending');
                        
                        IF v_user_usage > 0 THEN
                            SET p_message = 'You have already used this promo code';
                            SET p_discount_amount = 0;
                            SET p_final_amount = v_total_amount;
                            ROLLBACK;
                        ELSE
                            -- Calculate discount
                            IF v_promo_type = 'flat' THEN
                                SET p_discount_amount = v_promo_value;
                            ELSEIF v_promo_type = 'percent' THEN
                                SET p_discount_amount = (v_total_amount * v_promo_value / 100);
                            ELSE
                                SET p_discount_amount = 0;
                            END IF;
                            
                            -- Ensure discount doesn't exceed total
                            IF p_discount_amount > v_total_amount THEN
                                SET p_discount_amount = v_total_amount;
                            END IF;
                            
                            SET p_final_amount = v_total_amount - p_discount_amount;
                            
                            -- Update transaction with promo
                            UPDATE transactions
                            SET promo_code_id = v_promo_id,
                                discount_amount = p_discount_amount,
                                total_amount = p_final_amount,
                                updated_at = NOW()
                            WHERE id = p_transaction_id;
                            
                            -- Increment promo usage
                            UPDATE promo_codes
                            SET uses = uses + 1,
                                updated_at = NOW()
                            WHERE id = v_promo_id;
                            
                            COMMIT;
                            SET p_message = 'Promo code applied successfully';
                        END IF;
                    END IF;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_CreateTransaction');
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_ApplyPromoCode');
    }
};
