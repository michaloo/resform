DELIMITER $$

---- events

-- audit log trigger
CREATE TRIGGER `_prefix_events_after_insert`
    AFTER INSERT ON `_prefix_events`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.event_id, "INSERT event", @user)$$

CREATE TRIGGER `_prefix_events_after_update`
    AFTER UPDATE ON `_prefix_events`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.event_id, "UPDATE event", @user)$$

CREATE TRIGGER `_prefix_events_after_delete`
    AFTER DELETE ON `_prefix_events`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(OLD.event_id, "DELETE event", @user)$$

---- room_types

-- trigger creates pool of rooms for new room_type
CREATE TRIGGER `_prefix_room_types_after_insert`
    AFTER INSERT ON `_prefix_room_types`
    FOR EACH ROW BEGIN

        SET @i = 0;
        REPEAT
            SET @i = @i + 1;
            INSERT INTO _prefix_rooms(room_type_id) VALUES(NEW.room_type_id);
        UNTIL @i >= NEW.room_count
        END REPEAT;

        INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.room_type_id, "INSERT room_type", @user);
    END$$

-- trigger inserts or deletes rooms in relation to changed room_count column
CREATE TRIGGER `_prefix_room_types_after_update`
    AFTER UPDATE ON `_prefix_room_types`
    FOR EACH ROW proc:BEGIN

        INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.room_type_id, "UPDATE room_type", @user);

        IF OLD.room_count = NEW.room_count THEN
            LEAVE proc;
        END IF;

        SET @existing_room = 0;
        SELECT count(room_id) INTO @existing_rooms FROM _prefix_rooms WHERE room_type_id=NEW.room_type_id;
        SET @i = 0;
        SET @m = NEW.room_count - @existing_rooms;
        IF @m > 0 THEN
            REPEAT
                SET @i = @i + 1;
                INSERT INTO _prefix_rooms(room_type_id) VALUES(NEW.room_type_id);
            UNTIL @i >= @m
            END REPEAT;
        ELSEIF @m < 0 THEN
            REPEAT
                SET @i = @i + 1;
                DELETE FROM _prefix_rooms WHERE room_type_id = NEW.room_type_id ORDER BY room_id DESC LIMIT 1;
            UNTIL @i >= ( - @m)
            END REPEAT;
        END IF;

    END$$

CREATE TRIGGER `_prefix_room_types_after_delete`
    AFTER DELETE ON `_prefix_room_types`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(OLD.room_type_id, "DELETE room_type", @user)$$

-- trigger creates pool of rooms for new room_type
-- CREATE TRIGGER `_prefix_rooms_before_insert`
--     BEFORE INSERT ON `_prefix_rooms`
--     FOR EACH ROW BEGIN

--         DECLARE rooms_count MEDIUMINT(9) DEFAULT 0;
--         SELECT rooms_count INTO rooms_count FROM _prefix_room_types WHERE room_type_id = NEW.room_type_id;
--         REPEAT
--             SET @i = @i + 1;
--             INSERT INTO _prefix_rooms(room_type_id) VALUES(NEW.room_type_id);
--         UNTIL @i >= NEW.room_count
--         END REPEAT;
--     END$$

CREATE TRIGGER `_prefix_rooms_after_insert`
    AFTER INSERT ON `_prefix_rooms`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.room_id, "INSERT room", @user)$$

CREATE TRIGGER `_prefix_rooms_after_update`
    AFTER UPDATE ON `_prefix_rooms`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.room_id, "UPDATE room", @user)$$

CREATE TRIGGER `_prefix_rooms_after_delete`
    AFTER DELETE ON `_prefix_rooms`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(OLD.room_id, "DELETE room", @user)$$


---- transports

-- audit log trigger
CREATE TRIGGER `_prefix_transports_after_insert`
    AFTER INSERT ON `_prefix_transports`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.transport_id, "INSERT transport", @user);

CREATE TRIGGER `_prefix_transports_after_update`
    AFTER UPDATE ON `_prefix_transports`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.transport_id, "UPDATE transport", @user);

CREATE TRIGGER `_prefix_transports_after_delete`
    AFTER DELETE ON `_prefix_transports`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(OLD.transport_id, "DELETE transport", @user);


---- persons

-- trigger query for new room_id for new persons record
CREATE TRIGGER `_prefix_persons_before_insert`
    BEFORE INSERT ON `_prefix_persons`
    FOR EACH ROW proc:BEGIN

        SET @room_id = NULL;

        IF NEW.room_id IS NULL
            AND NEW.room_type_id IS NULL
            AND NEW.family_person_id IS NULL THEN
            LEAVE proc;
        END IF;

        SELECT _prefix_rooms_available(NEW.room_type_id, NEW.sex, NEW.family_person_id, NEW.is_family_guardian, NEW.room_id) INTO @room_id;

        IF @room_id IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'This room is not available';
        END IF;

        SET NEW.room_id = @room_id;

    END$$

-- trigger updates room data only for family_guardian persons
-- CREATE TRIGGER `_prefix_persons_after_insert`
--     AFTER INSERT ON `_prefix_persons`
--     FOR EACH ROW BEGIN
--
--         IF NEW.family_person_id IS NULL THEN
--             UPDATE _prefix_rooms SET sex = NEW.sex WHERE room_id = @room_id;
--         END IF;
--
--         IF NEW.family_guardian = true THEN
--             UPDATE _prefix_rooms SET family_person_id = NEW.person_id WHERE room_id = NEW.room_id;
--
--         END IF;
--
--         INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.person_id, "INSERT person", @user);
--
--     END$$

-- trigger makes sure that updated room_id is checked before saving
CREATE TRIGGER `_prefix_persons_before_update`
    BEFORE UPDATE ON `_prefix_persons`
    FOR EACH ROW proc:BEGIN

        IF OLD.room_id <=> NEW.room_id AND OLD.room_type_id <=> NEW.room_type_id THEN
            LEAVE proc;
        END IF;

        IF NEW.room_id IS NULL
            AND NEW.room_type_id IS NULL
            AND NEW.family_person_id IS NULL THEN
            LEAVE proc;
        END IF;

        -- we cannot move person who is a member of a family
        -- IF NEW.family_person_id IS NOT NULL THEN
        --     SET NEW.room_id = OLD.room_id;
        --     LEAVE proc;
        -- END IF;

        SET @room_id = NULL;

        SELECT _prefix_rooms_available(NEW.room_type_id, NEW.sex, NEW.family_person_id, NEW.is_family_guardian, NEW.room_id) INTO @room_id;

        IF @room_id IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'This room is not available';
        END IF;

        SET NEW.room_id = @room_id;

    END$$

-- trigger updates room data only for is_family_guardian persons
CREATE TRIGGER `_prefix_persons_after_update`
    AFTER UPDATE ON `_prefix_persons`
    FOR EACH ROW proc:BEGIN

        IF OLD.room_id = NEW.room_id
            OR NEW.is_family_guardian IS NULL THEN
            LEAVE proc;
        END IF;

        INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(NEW.person_id, "UPDATE person", @user);

    END$$

CREATE TRIGGER `_prefix_persons_after_delete`
    AFTER DELETE ON `_prefix_persons`
    FOR EACH ROW INSERT INTO _prefix_audit_logs(object_id, log, user) VALUES(OLD.person_id, "DELETE person", @user)$$

DELIMITER ;
