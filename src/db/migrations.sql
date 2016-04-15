
ALTER TABLE _prefix_persons
    ADD COLUMN is_reservation BOOLEAN NOT NULL DEFAULT 0 AFTER transport_id;
