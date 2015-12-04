DELIMITER $

DROP PROCEDURE IF EXISTS insertPackage $
CREATE PROCEDURE insertPackage ( IN Nome  VARCHAR(100), PrefixNome   VARCHAR(5000),
 Descrizione VARCHAR(10000), UML   VARCHAR(50), Padre INT(5), RelationType   ENUM('P','C'))
BEGIN
	START TRANSACTION;
	        INSERT INTO Package(Nome,PrefixNome,Descrizione,UML,Padre,RelationType,Time)
	        VALUES  (Nome,PrefixNome,Descrizione,UML,Padre,RelationType,CURRENT_TIMESTAMP);
	COMMIT;
END $