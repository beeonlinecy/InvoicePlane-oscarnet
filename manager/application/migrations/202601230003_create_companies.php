CREATE TABLE companies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    account_id INT UNSIGNED NOT NULL,

    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(191) NOT NULL,

    db_name VARCHAR(191) NOT NULL,
    db_user VARCHAR(191) NOT NULL,
    db_password VARBINARY(255) NOT NULL,

    path VARCHAR(255) NOT NULL, -- /companies/demo

    status ENUM('active','suspended','deleted') NOT NULL DEFAULT 'active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_companies_account
        FOREIGN KEY (account_id) REFERENCES accounts(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;