DELIMITER $
/*
Pre= CodReq contiene un valore intero valido
	 PkgL contiene una lista di valori interi (anche non validi) formati però dalla seguente sintassi:
	 CodAuto1,CodAuto2,CodAuto3,
	 */
DROP PROCEDURE IF EXISTS modifyRequisitiPackage $
CREATE PROCEDURE modifyRequisitiPackage  ( IN CodReq INT(5), PkgL VARCHAR(1000))
BEGIN
    START TRANSACTION;
    	CALL removeRequisitiPackage(CodReq);
    	CALL insertRequisitiPackage(CodReq,PkgL);
    COMMIT;
END $

DROP PROCEDURE IF EXISTS modifyPackageRequisiti $
CREATE PROCEDURE modifyPackageRequisiti  ( IN Pkg INT(5), ReqL VARCHAR(1000))
BEGIN
    START TRANSACTION;
    	CALL removePackageRequisiti(Pkg);
    	CALL insertPackageRequisiti(Pkg,ReqL);
    COMMIT;
END $