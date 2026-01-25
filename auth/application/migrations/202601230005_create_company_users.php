CREATE TABLE company_users (
    company_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,

    role ENUM('owner','admin','viewer') NOT NULL DEFAULT 'viewer',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (company_id, user_id),

    CONSTRAINT fk_cu_company
        FOREIGN KEY (company_id) REFERENCES companies(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_cu_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;