/*- Base de Données Loterie :
    * Déclencheur permettant d'insérer dans une table spécifique les résultats de gains :
       -> Principe :
           * Création d'un déclencheur sur T_LOT_ticketwinningt dans la table T_LOT_gainavalider l'ensemble des gagnants suite au tirage d'un lotoc
       -> Paramètres :
           * Aucun
       -> Etapes :
           * Création de la table T_LOT_gainavalider(id,fk_LEC,date,fk_LTC,fk_LWC) si elle n'existe pas.
           * Insertion des données dans la table T_LOT_gainavalider.
           * Attention ce déclencheur doit être prévu pour des bulks inserts dans la table T_LOT_ticketwinningt via des variables de table.
 */
CREATE TRIGGER Tr_LOT_gainvalider
ON T_LOT_personc
AFTER UPDATE
AS
BEGIN
	/** Insertion des données dans la table T_LOT_gainavalider.**/

	-- si la table stat n'existe pas , on cree la table --
	IF OBJECT_ID('T_LOT_gainvalider','U') IS  NULL
	BEGIN
			CREATE  TABLE  T_LOT_gainvalider
				(
				 id INT ,
				_fk_LEC NVARCHAR(400),
				 date DATETIME2(3),
				 _fk_LTC INT,
				 _fk_LWC INT,
				)
	END
	-- sinon, on prépare une modification dans la table --
	ELSE
		BEGIN
				UPDATE T_LOT_gainvalider
				SET _fk_LEC =
					 (
					 SELECT LEC_id
					 FROM T_LOT_personc AS P
					 WHERE P.LEC_id = INSERTED.fk_lEC
					 )
				FROM  T_LOT_gainvalider Y
				 INNER JOIN Inserted I ON Y.id = I.id
				WHERE I.LEC_id IS NULL
		END;
END;

