DELIMITER $

DROP PROCEDURE IF EXISTS modifyRelatedPackage $
CREATE PROCEDURE modifyRelatedPackage(IN PkgSubject INT(5), PkgList VARCHAR(1000))
BEGIN
    START TRANSACTION;
        CALL removeRelatedPackage(PkgSubject);
        CALL insertRelatedPackage(PkgSubject,PkgList);
    COMMIT;
END $