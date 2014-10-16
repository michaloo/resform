
CREATE VIEW _prefix_rooms_space_count AS
    SELECT
        rt.event_id,
        r.*,
        count(p.person_id) AS occupied_space_count,
        rt.space_count,
        rt.space_count - count(p.person_id) AS free_space_count,
        rt.name AS room_type_name
    FROM _prefix_rooms AS r
    LEFT JOIN _prefix_persons AS p USING(room_id)
    LEFT JOIN _prefix_room_types AS rt ON r.room_type_id = rt.room_type_id
    GROUP BY r.room_id;

CREATE VIEW _prefix_room_types_space_count AS
    SELECT
        room_type_id,
        room_type_name,
        SUM(occupied_space_count) AS occupied_space_count,
        SUM(free_space_count) AS free_space_count
    FROM _prefix_rooms_space_count GROUP BY room_type_id;


CREATE VIEW _prefix_persons_family AS
    SELECT p.*, GROUP_CONCAT(CONCAT_WS(' ', pc.first_name, pc.last_name)) AS children
    FROM _prefix_persons AS p
    LEFT JOIN _prefix_persons AS pc ON (p.person_id = pc.family_person_id)
    WHERE p.family_guardian = true
    GROUP BY p.person_id;
