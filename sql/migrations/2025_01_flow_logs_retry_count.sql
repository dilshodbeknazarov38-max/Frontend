UPDATE leads SET flow_status = 'sent' WHERE flow_status = 'success';

ALTER TABLE leads
    ADD COLUMN retry_count INT NOT NULL DEFAULT 0 AFTER flow_status,
    MODIFY COLUMN flow_status ENUM('pending','sent','failed') NOT NULL DEFAULT 'pending';

CREATE TABLE IF NOT EXISTS flow_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NOT NULL,
    request_payload JSON NOT NULL,
    response_status INT NULL,
    response_body TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_flow_logs_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
