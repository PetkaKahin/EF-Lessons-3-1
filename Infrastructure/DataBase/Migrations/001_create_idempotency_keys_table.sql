CREATE TABLE IF NOT EXISTS idempotency_keys (
    id TEXT PRIMARY KEY,
    resource_id TEXT NOT NULL,
    request_hash TEXT NOT NULL
);
