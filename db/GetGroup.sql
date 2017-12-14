use zoodb;

DROP FUNCTION IF EXISTS GetGroup;
DELIMITER $$

CREATE FUNCTION GetGroup() RETURNS Text CHARSET utf8
    DETERMINISTIC
    COMMENT 'Zoo groups of animals'
BEGIN
  DECLARE res TEXT CHARSET utf8;
  DECLARE v_name VARCHAR(50) CHARSET utf8;
  DECLARE v_id INT UNSIGNED;
  DECLARE done INT DEFAULT FALSE;
  DECLARE cursorGroup CURSOR FOR select g.id, g.name from `group` g order by g.name;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  Set res = Concat('<groups>\n');
  OPEN cursorGroup;
  read_loop: LOOP
    FETCH cursorGroup INTO v_id, v_name;
    IF done THEN
      LEAVE read_loop;
    END IF;
    SET res = Concat(res, '<group id="', v_id, '">', v_name, '</group>\n');
  END LOOP;
  CLOSE cursorGroup;
  Set res = Concat(res, '</groups>');
  RETURN(res);
END;

$$

DELIMITER ;
