CREATE DATABASE zoodb;
CREATE USER 'zoo'@'localhost' IDENTIFIED BY 'zootable!';
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP,CREATE ROUTINE,ALTER ROUTINE,EXECUTE ON zoodb.* TO 'zoo'@'localhost';
flush privileges;

use zoodb;

CREATE TABLE `group` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='groups of aniamls';


insert into `group` (name) values (Upper('Mammal'));
insert into `group` (name) values (Upper('Bird'));
insert into `group` (name) values (Upper('Reptile'));

CREATE TABLE `list` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `count` int UNSIGNED NOT NULL,
  `group_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  FOREIGN KEY (`group_id`) REFERENCES `group`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='list of animals';

insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Elephant'), 3, (select id from `group` where upper(name) like Upper('Mammal')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Monkey'), 7, (select id from `group` where upper(name) like Upper('Mammal')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Zebra'), 5, (select id from `group` where upper(name) like Upper('Mammal')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Lion'), 2, (select id from `group` where upper(name) like Upper('Mammal')));

insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Condor'), 1, (select id from `group` where upper(name) like Upper('Bird')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Goose'), 6, (select id from `group` where upper(name) like Upper('Bird')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Eagle'), 2, (select id from `group` where upper(name) like Upper('Bird')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Crane'), 8, (select id from `group` where upper(name) like Upper('Bird')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Parrot'), 5, (select id from `group` where upper(name) like Upper('Bird')));

insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('turtle'), 3, (select id from `group` where upper(name) like Upper('Reptile')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Snake'), 5, (select id from `group` where upper(name) like Upper('Reptile')));
insert into `list` (`name`, `count`, `group_id`) 
  values (Upper('Crocodile'), 4, (select id from `group` where upper(name) like Upper('Reptile')));