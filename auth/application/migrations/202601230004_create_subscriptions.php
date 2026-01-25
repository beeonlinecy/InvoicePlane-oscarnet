CREATE TABLE subscriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    account_id INT UNSIGNED NOT NULL,
    plan_id INT UNSIGNED NOT NULL,

    status ENUM('active','past_due','canceled') NOT NULL DEFAULT 'active',

    started_at DATETIME NOT NULL,
    ends_at DATETIME NULL,

    CONSTRAINT fk_sub_account
        FOREIGN KEY (account_id) REFERENCES accounts(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_sub_plan
        FOREIGN KEY (plan_id) REFERENCES plans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;