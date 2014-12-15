
CREATE VIEW _prefix_rooms_space_count AS
    SELECT
        rt.event_id,
        rt.price,
        r.*,
        count(p.person_id) AS occupied_space_count,
        rt.space_count,
        rt.space_count - count(p.person_id) AS free_space_count,
        rt.name AS room_type_name,
        p.sex AS sex,
        IF(p.is_family_guardian, person_id, NULL) AS family_person_id
    FROM _prefix_rooms AS r
    LEFT JOIN _prefix_persons_with_age AS p ON p.room_id = r.room_id AND p.age > 3
    LEFT JOIN _prefix_room_types AS rt ON r.room_type_id = rt.room_type_id
    GROUP BY r.room_id, p.sex;

CREATE VIEW _prefix_room_types_space_count AS
    SELECT
        event_id,
        room_type_id,
        room_type_name,
        price,
        SUM(occupied_space_count) AS occupied_space_count,
        SUM(free_space_count) AS free_space_count
    FROM _prefix_rooms_space_count GROUP BY room_type_id;


CREATE VIEW _prefix_persons_family AS
    SELECT p.*, GROUP_CONCAT(CONCAT_WS(' ', pc.first_name, pc.last_name)) AS children
    FROM _prefix_persons AS p
    LEFT JOIN _prefix_persons AS pc ON (p.person_id = pc.family_person_id)
    WHERE p.is_family_guardian = true
    GROUP BY p.person_id;

CREATE VIEW _prefix_persons_with_age AS
    SELECT
        p.*,
        TIMESTAMPDIFF( YEAR, p.birth_date, CURDATE() ) AS age
    FROM _prefix_persons AS p;


CREATE VIEW _prefix_persons_with_price AS
    SELECT
        p.*,
        CASE
            WHEN p.age <= 3 THEN 0
            WHEN p.age <= 10 THEN (rt.price + rrt.price) * 0.5
            ELSE rt.price + rrt.price
        END AS price
    FROM _prefix_persons_with_age AS p
    LEFT JOIN _prefix_rooms AS rr USING (room_id)
    LEFT JOIN _prefix_room_types AS rrt ON rr.room_type_id = rrt.room_type_id
    LEFT JOIN _prefix_transports AS rt USING (transport_id);

CREATE VIEW _prefix_persons_with_total_price AS
    SELECT
        p.*,
        (
            SELECT SUM(rc.price) FROM _prefix_persons_with_price AS rc
            WHERE rc.family_person_id = p.person_id
            GROUP BY rc.family_person_id
        ) AS price_family
    FROM _prefix_persons_with_price AS p
    WHERE p.family_person_id IS NULL
