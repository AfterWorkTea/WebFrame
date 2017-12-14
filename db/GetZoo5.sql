use zoodb;

DROP FUNCTION IF EXISTS GetZoo5;
DELIMITER $$

CREATE FUNCTION GetZoo5(v_sort char(1), 
                        v_group_id INT UNSIGNED,
			v_from INT UNSIGNED, 
                        v_offset INT UNSIGNED) 
                  RETURNS Text CHARSET utf8
    DETERMINISTIC
    COMMENT 'Zoo animals'
BEGIN
  DECLARE res TEXT CHARSET utf8;
  DECLARE v_id INT UNSIGNED;
  DECLARE v_name VARCHAR(50) CHARSET utf8;
  DECLARE v_group_name VARCHAR(50) CHARSET utf8;
  DECLARE v_count INT UNSIGNED;
  DECLARE v_sel_count INT UNSIGNED;
  DECLARE done INT DEFAULT FALSE;
  DECLARE v_len INT UNSIGNED;
  DECLARE cursorZooN CURSOR FOR 
      select l.id, l.name, l.count, g.name, LENGTH(l.name) 
	   from list l join `group` g on g.id = l.group_id
      where (g.id = v_group_id or v_group_id = 0)
	  order by l.name, g.name, l.count
      limit v_from, v_offset;
  DECLARE cursorZooG CURSOR FOR 
      select l.id, l.name, l.count, g.name, LENGTH(l.name) 
       from list l join `group` g on g.id = l.group_id
      where (g.id = v_group_id or v_group_id = 0)
      order by g.name, l.name, l.count
      limit v_from, v_offset;
  DECLARE cursorZooC CURSOR FOR 
      select l.id, l.name, l.count, g.name, LENGTH(l.name) 
       from list l join `group` g on g.id = l.group_id
      where (g.id = v_group_id or v_group_id = 0)
      order by l.count, l.name, g.name
      limit v_from, v_offset;
  DECLARE cursorZooL CURSOR FOR 
      select l.id, l.name, l.count, g.name, LENGTH(l.name) 
       from list l join `group` g on g.id = l.group_id
      where (g.id = v_group_id or v_group_id = 0)
      order by 5, l.name, g.name, l.count
      limit v_from, v_offset;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  Set v_sel_count = (select count(l.id) from list l 
                       join `group` g on g.id = l.group_id
                      where (g.id = v_group_id or v_group_id = 0));
  IF v_sort is NULL then
     SET v_sort = 'N';
  END IF;
  Set v_sort = Upper(v_sort);
  IF FIND_IN_SET(v_sort, 'N,G,C,L') = 0 Then
    SET v_sort = 'N';
  END IF;
  Set res = Concat('<zoo count="', v_sel_count, '">\n');
  CASE v_sort
     WHEN 'N' THEN OPEN cursorZooN;
     WHEN 'G' THEN OPEN cursorZooG;
     WHEN 'C' THEN OPEN cursorZooC;
     WHEN 'L' THEN OPEN cursorZooL;
  END CASE;
  read_loop: LOOP
	CASE v_sort
       WHEN 'N' THEN FETCH cursorZooN INTO v_id, v_name, v_count, v_group_name, v_len;
       WHEN 'G' THEN FETCH cursorZooG INTO v_id, v_name, v_count, v_group_name, v_len;
       WHEN 'C' THEN FETCH cursorZooC INTO v_id, v_name, v_count, v_group_name, v_len;
       WHEN 'L' THEN FETCH cursorZooL INTO v_id, v_name, v_count, v_group_name, v_len;
	END CASE;
    IF done THEN
      LEAVE read_loop;
    END IF;
    SET res = Concat(res, '<animal count="', v_count, 
                                   '" id="', v_id, 
                                '" group="', v_group_name, 
                                  '" len="', v_len, '">', 
                           v_name, '</animal>\n');
  END LOOP;
  CASE v_sort
     WHEN 'N' THEN CLOSE cursorZooN;
     WHEN 'G' THEN CLOSE cursorZooG;
     WHEN 'C' THEN CLOSE cursorZooC;  
     WHEN 'L' THEN CLOSE cursorZooL;
  END CASE;
  Set res = Concat(res, '</zoo>');
  RETURN(res);
END;

$$

DELIMITER ;
