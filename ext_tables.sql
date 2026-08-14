CREATE TABLE tx_atanalyticslite_visit (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    page_uid INT NOT NULL,
    language_uid INT DEFAULT 0,
    visit_date DATE NOT NULL,
    tstamp INT NOT NULL,
    referrer_domain VARCHAR(255) DEFAULT '',
    device_type VARCHAR(50) DEFAULT '',
    ip_hash VARCHAR(255) DEFAULT ''
);

CREATE TABLE tx_atanalyticslite_daily_stat (
    uid INT AUTO_INCREMENT PRIMARY KEY,
    stat_date DATE NOT NULL,
    page_uid INT NOT NULL,
    language_uid INT DEFAULT 0,
    visits INT NOT NULL DEFAULT 0,
    tstamp INT NOT NULL,
    crdate INT NOT NULL,
    UNIQUE KEY uniq_stat (stat_date, page_uid, language_uid)
);