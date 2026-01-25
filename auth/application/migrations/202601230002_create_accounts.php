CREATE TABLE accounts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    owner_user_id INT UNSIGNED NOT NULL,
    name VARCHAR(191) NOT NULL,

    status ENUM('active','suspended','deleted') NOT NULL DEFAULT 'active',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_accounts_owner
        FOREIGN KEY (owner_user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;