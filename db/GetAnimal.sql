use zoodb;

DROP FUNCTION IF EXISTS GetAnimal;
DELIMITER $$

CREATE FUNCTION GetAnimal(v_id INT UNSIGNED) RETURNS Text CHARSET utf8
    DETERMINISTIC
    COMMENT 'Selected animal from Zoo'
BEGIN
  DECLARE res TEXT CHARSET utf8;
  DECLARE v_name VARCHAR(50) CHARSET utf8;
  DECLARE v_count INT UNSIGNED;
  DECLARE v_group_id INT UNSIGNED;
  Select l.name, l.count, l.group_id
    into v_name, v_count, v_group_id
    from list l
    where l.id = v_id;	
  SET res = Concat('<animal count="', v_count, 
                                   '" id="', v_id,
                                   '" groupid="', v_group_id,
                           '">', v_name, '</animal>\n');  
  RETURN(res);
END;

$$

DELIMITER ;
