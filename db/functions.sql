DELIMITER $$
CREATE FUNCTION _prefix_rooms_available(
    new_room_type_id MEDIUMINT(9),
    new_sex ENUM('male', 'female'),
    new_family_person_id MEDIUMINT(9),
    new_family_guardian BOOLEAN,
    new_room_id MEDIUMINT(9))
RETURNS MEDIUMINT
DETERMINISTIC
BEGIN
    DECLARE return_room_id MEDIUMINT(9);
    SELECT room_id INTO return_room_id
    FROM _prefix_rooms_space_count
    WHERE (
        -- room is empty (only if we are not adding a family member)
        (occupied_space_count = 0 AND new_family_person_id IS NULL)
        -- room is already occupied by same sex person (and we are not adding family_guardian)
        OR (sex = new_sex AND new_family_guardian = false AND new_family_person_id IS NULL AND free_space_count > 0)
        -- room is already occupied by family_person
        OR (new_family_person_id IS NOT NULL AND family_person_id = new_family_person_id AND free_space_count > 0)
    )
    -- if new_room_id is provided we check only that room
    AND (IF(new_room_id IS NULL, true, false) OR room_id = new_room_id)
    -- we only query selected new_room_type_id
    AND (IF(new_room_type_id IS NULL, true, false) OR room_type_id = new_room_type_id)
    ORDER BY free_space_count ASC, room_id LIMIT 1;
    RETURN return_room_id;
END$$
DELIMITER ;
