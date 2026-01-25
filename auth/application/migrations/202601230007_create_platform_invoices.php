CREATE TABLE platform_invoices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    account_id INT UNSIGNED NOT NULL,

    amount DECIMAL(10,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',

    status ENUM('draft','issued','paid','failed') NOT NULL DEFAULT 'issued',

    period_start DATE NOT NULL,
    period_end DATE NOT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_pi_account
        FOREIGN KEY (account_id) REFERENCES accounts(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;