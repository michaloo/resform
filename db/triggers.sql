
DROP TRIGGER IF EXISTS `_prefix_room_types_after_insert`;
DROP TRIGGER IF EXISTS `_prefix_room_types_after_update`;

DROP TRIGGER IF EXISTS `_prefix_persons_before_insert`;
DROP TRIGGER IF EXISTS `_prefix_persons_after_insert`;

DROP TRIGGER IF EXISTS `_prefix_persons_before_update`;
DROP TRIGGER IF EXISTS `_prefix_persons_after_update`;

DELIMITER $$

CREATE TRIGGER `_prefix_room_types_after_insert`
    AFTER INSERT ON `_prefix_room_types`
    FOR EACH ROW BEGIN
        SET @i = 0;
        REPEAT
            SET @i = @i + 1;
            INSERT INTO _prefix_rooms(room_type_id) VALUES(NEW.room_type_id);
        UNTIL @i >= NEW.room_count
        END REPEAT;
    END$$

CREATE TRIGGER `_prefix_room_types_after_update`
    AFTER UPDATE ON `_prefix_room_types`
    FOR EACH ROW BEGIN

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

CREATE TRIGGER `_prefix_persons_before_insert`
    BEFORE INSERT ON `_prefix_persons`
    FOR EACH ROW BEGIN

        SET @room_id = NULL;

        SELECT _prefix_rooms_available(NEW.sex, NEW.family_person_id, NEW.family_guardian, NULL) INTO @room_id;
        -- SELECT room_id INTO @room_id
        --     FROM _prefix_rooms_space_count
        --     WHERE
        --         -- room is empty (only if we are not adding a family member)
        --         (occupied_space_count = 0 AND NEW.family_person_id IS NULL)
        --         -- room is already occupied by same sex person (and we are not adding family_guardian)
        --         OR (sex = NEW.sex AND NEW.family_guardian = false AND NEW.family_person_id IS NULL AND free_space_count > 0)
        --         -- room is already occupied by family_person
        --         OR (NEW.family_person_id IS NOT NULL AND family_person_id = NEW.family_person_id AND free_space_count > 0)
        --     ORDER BY free_space_count ASC, room_id LIMIT 1;

        IF NEW.family_person_id IS NULL THEN
            UPDATE _prefix_rooms SET sex = NEW.sex WHERE room_id = @room_id;
        END IF;

        SET NEW.room_id = @room_id;

    END$$

CREATE TRIGGER `_prefix_persons_after_insert`
    AFTER INSERT ON `_prefix_persons`
    FOR EACH ROW BEGIN

        IF NEW.family_guardian = true THEN
            UPDATE _prefix_rooms SET family_person_id = NEW.person_id WHERE room_id = NEW.room_id;

        END IF;

    END$$

CREATE TRIGGER `_prefix_persons_before_update`
    BEFORE UPDATE ON `_prefix_persons`
    FOR EACH ROW BEGIN

        SET @room_id = NULL;

        SELECT _prefix_rooms_available(NEW.sex, NEW.family_person_id, NEW.family_guardian, NEW.room_id) INTO @room_id;
        SET NEW.room_id = @room_id;

        IF NEW.family_person_id IS NULL THEN
            UPDATE _prefix_rooms SET sex = NEW.sex WHERE room_id = @room_id;
        END IF;

    END$$

CREATE TRIGGER `_prefix_persons_after_update`
    AFTER UPDATE ON `_prefix_persons`
    FOR EACH ROW BEGIN

        IF NEW.family_guardian = true THEN
            UPDATE _prefix_rooms SET family_person_id = NEW.person_id WHERE room_id = NEW.room_id;

        END IF;

    END$$


DELIMITER ;
