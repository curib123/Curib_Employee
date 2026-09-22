ALTER TABLE users
    ADD COLUMN profile_picture VARCHAR(255) NULL AFTER must_change_password,
    ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER profile_picture;
