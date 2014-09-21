\. db/tests/helpers.sql

DROP PROCEDURE IF EXISTS `_prefix_test`;

DELIMITER $$

CREATE PROCEDURE _prefix_test()
BEGIN

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 'An error has occurred.';
    END;

    DELETE FROM _prefix_persons;
    DELETE FROM _prefix_events;

    SELECT 'insert new event';
    INSERT INTO _prefix_events(name) VALUES('test');

    SET @event_id = LAST_INSERT_ID();


    SELECT 'insert new room type';
    INSERT INTO _prefix_room_types(name, space_count, bathroom, room_count, price, event_id)
        VALUES('five person room - 100 items', 5, true, 100, 50.00, @event_id);

    SET @first_room_type_id = LAST_INSERT_ID();

    SELECT 'insert new room type';
    INSERT INTO _prefix_room_types(name, space_count, bathroom, room_count, price, event_id)
        VALUES('eight person room - 200 items', 8, true, 200, 50.00, @event_id);

    SET @second_room_type_id = LAST_INSERT_ID();


    SELECT 'insert new transport';
    INSERT INTO _prefix_transports(name, price, event_id)
        VALUES('bus ride', 0.5, @event_id);

    SET @transport_id = LAST_INSERT_ID();

    SELECT 'add 500 people into five persons room';
    SELECT _prefix_gen_people(500, @transport_id, @first_room_type_id);

    SELECT 'add 1600 people into eight persons room';
    SELECT _prefix_gen_people(1600, @transport_id, @second_room_type_id);

    UPDATE _prefix_room_types SET room_count = 200 WHERE room_type_id = @first_room_type_id;

    UPDATE _prefix_room_types SET room_count = 150 WHERE room_type_id = @first_room_type_id;

    SELECT * FROM _prefix_room_types;

END$$

DELIMITER ;

CALL _prefix_test();
