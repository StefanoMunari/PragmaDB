DELIMITER $
/*
Pre= PkgSubject contiene 1 valore non vuoto corrispondente a 1 package(CodAuto), 
PkgList contiene una lista non vuota formata dalla seguente sintassi:
CodAuto1,CodAuto2,CodAuto3,
Quindi una lista di package correlati a PkgSubject.
All'interno della procedura viene controllato che tutti gli elementi della lista esistano prima di essere inseriti
*/
DROP PROCEDURE IF EXISTS insertRelatedPackage $
CREATE PROCEDURE insertRelatedPackage ( IN PkgSubject INT(5), PkgList VARCHAR(1000))
BEGIN
    DECLARE legal TINYINT DEFAULT 1;
    DECLARE CodPkg INT(5) DEFAULT 0;
    START TRANSACTION;
        CALL legalPackageList(legal,PkgList);
        IF legal > 0
        THEN
            WHILE PkgList <> ''
            DO
                CALL parseIdList(PkgList,',',CodPkg);/*si trova in UseCase->utility->uc_FK_utility*/
                INSERT INTO RelatedPackage(Pack1,Pack2)
                VALUES (PkgSubject,CodPkg);
                CALL updatePackageTime(CodPkg);/*aggiorna Time di PackL, cioè di tutti i package nella lista correlata*/
            END WHILE;
            CALL updatePackageTime(PkgSubject);/*aggiorna Time di Package Subject*/
        END IF;
    COMMIT;
END $
