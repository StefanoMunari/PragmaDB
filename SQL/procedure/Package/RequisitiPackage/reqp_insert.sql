DELIMITER $
/*

Pre= CodReq contiene un valore valido
	 PkgL contiene una lista di valori interi (anche non validi) formati però dalla seguente sintassi:
	 CodAuto1,CodAuto2,CodAuto3,
	 */
DROP PROCEDURE IF EXISTS insertRequisitiPackage $
CREATE PROCEDURE insertRequisitiPackage  ( IN CodReq INT(5), PkgL VARCHAR(1000))
BEGIN
	DECLARE legal TINYINT DEFAULT 1;
	DECLARE CodPkg INT(5) DEFAULT NULL;

	CALL legalPackageList(legal,PkgL);
	IF CodReq IS NOT NULL AND legal >0 /*controllo in più per CodReq*/
	THEN
        WHILE PkgL <> ''
        DO
            CALL parseIdList(PkgL,',',CodPkg);/*si trova in UseCase->utility->uc_FK_utility*/
            INSERT INTO RequisitiPackage(CodReq,CodPkg)
            VALUES (CodReq,CodPkg);
            CALL updatePackageTime(CodPkg);
        END WHILE;
    END IF;
END $

DROP PROCEDURE IF EXISTS insertPackageRequisiti $
CREATE PROCEDURE insertPackageRequisiti  ( IN Pkg INT(5), ReqL VARCHAR(1000))
BEGIN
	DECLARE legal TINYINT DEFAULT 1;
	DECLARE CodReq INT(5) DEFAULT NULL;

	CALL legalListRequisiti(legal,ReqL);
	IF Pkg IS NOT NULL AND legal >0 /*controllo in più per Pkg*/
	THEN
        WHILE ReqL <> ''
        DO
            CALL parseIdList(ReqL,',',CodReq);/*si trova in UseCase->utility->uc_FK_utility*/
            INSERT INTO RequisitiPackage(CodPkg,CodReq)
            VALUES (Pkg,CodReq);
        END WHILE;
        CALL updatePackageTime(Pkg);
    END IF;
END $