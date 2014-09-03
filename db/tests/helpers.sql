DROP FUNCTION IF EXISTS _prefix_gen_people;

DELIMITER $$
CREATE FUNCTION _prefix_gen_people(
    count MEDIUMINT(9),
    new_transport_id MEDIUMINT(9),
    new_room_type_id MEDIUMINT(9))
RETURNS MEDIUMINT
DETERMINISTIC
BEGIN
    SET @i = 0;
    REPEAT
        SET @i = @i + 1;
        INSERT INTO _prefix_persons(transport_id, room_type_id, first_name, last_name, birth_date, email, phone, sex)
        VALUES(new_transport_id, new_room_type_id, "George", "Smiths", '1990-12-31', 'test@test.com', '123', 'male');
    UNTIL @i >= count
    END REPEAT;
    RETURN @i;
END$$
DELIMITER ;