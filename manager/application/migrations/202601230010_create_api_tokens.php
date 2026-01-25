CREATE TABLE api_tokens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    company_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,

    token CHAR(64) NOT NULL UNIQUE,

    permissions JSON NOT NULL,

    last_used_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_api_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
