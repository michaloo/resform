
DROP PROCEDURE IF EXISTS `wp_resform_test`;

DELIMITER $$

CREATE PROCEDURE wp_resform_test()
BEGIN

    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 'An error has occurred.';
    END;

    DELETE FROM wp_resform_persons;
    DELETE FROM wp_resform_events;

    SELECT 'insert new event';
    INSERT INTO wp_resform_events(name) VALUES('test');

    SET @event_id = LAST_INSERT_ID();


    SELECT 'insert new room type';
    INSERT INTO wp_resform_room_types(name, space_count, has_bathroom, room_count, price, event_id)
        VALUES('two person room - 5 items', 2, true, 5, 50.00, @event_id);

    SET @room_type_id = LAST_INSERT_ID();


    SELECT 'insert new transport';
    INSERT INTO wp_resform_transports(name, price, event_id)
        VALUES('bus ride', 0.5, @event_id);

    SET @transport_id = LAST_INSERT_ID();

    SELECT count(room_id) INTO @rooms_count FROM wp_resform_rooms;

    SELECT @event_id, @room_type_id, @transport_id, @rooms_count;

    IF @rooms_count != 5 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bad rooms count value';
    END IF;



    SELECT 'insert first man';
    INSERT INTO wp_resform_persons(event_id, transport_id, room_type_id, first_name, last_name, birth_date, email, phone, sex)
        VALUES(@event_id, @transport_id, @room_type_id, "John", "Smiths", '1990-12-31', 'test@test.com', '123', 'male');

    SET @first_man_id = LAST_INSERT_ID();

    SELECT room_id INTO @first_room_id FROM wp_resform_persons WHERE person_id = @first_man_id;



    SELECT 'insert second man';
    INSERT INTO wp_resform_persons(event_id, transport_id, room_type_id, first_name, last_name, birth_date, email, phone, sex)
        VALUES(@event_id, @transport_id, @room_type_id, "George", "Smiths", '1990-12-31', 'test@test.com', '123', 'male');

    SELECT occupied_space_count INTO @occupied_space_count FROM wp_resform_rooms_space_count WHERE room_id = @first_room_id;

    IF @occupied_space_count != 2 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'There should be two man in the first room now';
    END IF;



    SELECT 'insert fist woman';
    INSERT INTO wp_resform_persons(event_id, transport_id, room_type_id, first_name, last_name, birth_date, email, phone, sex)
        VALUES(@event_id, @transport_id, @room_type_id, "Ann", "Apple", '1990-12-31', 'test@test.com', '123', 'female');

    SET @first_woman_id = LAST_INSERT_ID();

    SELECT room_id INTO @second_room_id FROM wp_resform_persons WHERE person_id = @first_woman_id;

    SELECT occupied_space_count INTO @occupied_space_count FROM wp_resform_rooms_space_count WHERE room_id = @second_room_id;

    IF @occupied_space_count != 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'There should be one woman in the second room now';
    END IF;


    SELECT 'insert first family guardian';
    INSERT INTO wp_resform_persons(event_id, transport_id, room_type_id, first_name, last_name, birth_date, email, phone, sex, is_family_guardian)
        VALUES(@event_id, @transport_id, @room_type_id, "Mary", "Apple", '1990-12-31', 'test@test.com', '123', 'female', true);

    SET @first_family_guardian_id = LAST_INSERT_ID();



    SELECT 'insert a family memeber';
    INSERT INTO wp_resform_persons(event_id, transport_id, first_name, last_name, birth_date, email, phone, sex, family_person_id)
        VALUES(@event_id, @transport_id, "Theresa", "Apple", '1990-12-31', 'test@test.com', '123', 'female', @first_family_guardian_id);



    SELECT 'room is too small to add another family memeber - should trigger an error';
    INSERT IGNORE INTO wp_resform_persons(event_id, transport_id, first_name, last_name, birth_date, email, phone, sex, family_person_id)
        VALUES(@event_id, @transport_id, "Jenna", "Apple", '1990-12-31', 'test@test.com', '123', 'female', @first_family_guardian_id);



    SELECT 'try to move first man to an empty room';
    SELECT wp_resform_rooms_available(@room_type_id, 'male', NULL, false, NULL) INTO @new_room_id;
    UPDATE wp_resform_persons SET room_id = @new_room_id WHERE person_id = @first_man_id;



    SELECT 'try moving to wrong room - should trigger an error';
    UPDATE IGNORE wp_resform_persons SET room_id = @new_room_id WHERE person_id = @first_woman_id;



    SELECT * FROM wp_resform_rooms_space_count;

    SHOW ERRORS;
END$$

DELIMITER ;

CALL wp_resform_test();
