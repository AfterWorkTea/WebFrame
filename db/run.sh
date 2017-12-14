#!/bin/bash
mysql -u root -p$1 < ./create_db.sql
mysql -u zoo -pzootable! < ./GetZoo5.sql
mysql -u zoo -pzootable! < ./GetGroup.sql
mysql -u zoo -pzootable! < ./GetAnimal.sql

