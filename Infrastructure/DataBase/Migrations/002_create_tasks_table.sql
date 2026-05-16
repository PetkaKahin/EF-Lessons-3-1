CREATE TABLE IF NOT EXISTS tasks (
    id TEXT PRIMARY KEY,
    title TEXT NOT NULL,
    description TEXT DEFAULT NULL,
    status TEXT NOT NULL,
    createdAt TEXT NOT NULL
);
