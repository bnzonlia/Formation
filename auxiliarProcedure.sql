/*Base de Données Loterie : Procédure de Création dynamique de tables types, états selon un modèle prédéfini en table
       -> Principe :
           * Création d'une procédure permettant de créer dynamiquement tout type de tables d'états, types selon 3 tables nommées T_UNI_tablex, T_UNI_tablem et T_UNI_tablen
           * La procédure prend en paramètre :
               @UTX_id -- id du modèle de référence pour la génération des tables
               @SUBPROJECT_NAME   -- nom du sous project dess tables (ex: T_LOT_loto, T_LOT_ticket)
               @SUBPROJECT_CODE -- code table du sous projet (en deux lettres donc puisque le suffixe dépend des tables générées; exemple 'LL' pour T_LOT_loto, 'LT' pour T_LOT_ticket)
       -> Structure des Tables :
            * T_UNI_typey (UYY_id, UYY_description, UYY_datatype)
                Table listant tous les types de datatype possibles, datetime2, nvarchar, varchar, tinyint, int etc...
            * T_UNI_tabley (UTY_id, UTY_description, UTY_suffix, UTY_key)
               Table listant toutes les tables 'génériques' possibles (états - e -; types - y -; surtypes - w-; etc...)
                Le UTY_suffix permet également de construire le code table de trois lettres grace à la concatenation SUBPROJECT_CODE + UTY_suffix
            * T_UNI_tablex (UTX_id, UTX_datestart, UTX_dateend, UTX_fk_UTE_valid)
               Table listant les modèles de création de tables.
               Elle contient un état valid qu'il faut checker lors de l'appel de la procédure.
            * T_UNI_tablem (UTM_id, UTM_fk_UTX, UTM_fk_UTY, UTM_fk_UTE_valid)
               Table listant les types de tables à générer selon le modèle avec des états de validité.
                On référence le type de table à créer avec le UTM_fk_UTY.
            * T_UNI_tablen (UTN_id, UTN_fk_UTM, UTN_name, UTN_fk_UYY, UTN_precision, UTN_scale, UTN_length, UTN_fk_UTE_valid)
                Table listant les noms de colonnes (le name de la column mais attention sans le code table), leur type et leur précision
            * T_UNI_tablep (UTP_id, UTP_description)
                Table listant les paramètres/contraintes possibles pour les colonnes (primary key, auto incrément, not null)
            * T_UNI_tablev (UTV_id, UTV_fk_UTN, UTV_fk_UTP, UTV_fk_UTE_valid)
                 Table listant les valeurs possibles pour les colonnes des tables génériques. C'est donc un pvc ici puisque les contraintes ne sont pas toujours présentes sur les colonnes et peuvent dépendre de nombreux facteurs. Il n'y a pas de value puisque la simple relation indique que la contrainte doit être créée et donc ensuite il s'agit de créer le script SQL.
                  S'il existe une ou plusieurs contraintes pour une colonne d'une table générique, cela affecte donc le script SQL de génération de la table.
*/

CREATE PROCEDURE P_LOT_dynamictable
AS
 DECLARE @UTX_id INT,
 DECLARE @SUBPROJECT_NAME VARCHAR(500),
 DECLARE @SUBPROJECT_CODE VARCHAR(3);
BEGIN
-- on boucle sur les tablem dont la date est valide(dans tablex) --
DECLARE C_TABLE_LOCATION LOCAL CURSOR FOR
  SELECT UTM_id
  FROM T_UNI_tablem
    INNER JOIN T_UNI_tablex ON UTM_fk_UTX = UTX_id AND UTX_id=@UTX_id AND @CURRENT_DATE BETWEEN UTX_datestart AND UTX_dateend
  OPEN C_TABLE_LOCATION;
  FETCH NEXT FROM C_TABLE_LOCATION INTO @UTM_id;
  WHILE @@FETCH_STATUS = 0
  BEGIN
	DECLARE @SQL NVARCHAR(4000);
	SET @UTY_suffix = select UTY_suffix
						FROM T_UNI_tabley
						INNER JOIN T_UNI_tablem ON UTM_fk_UTY=UTY_id ;

set @SQL= 'Create table '+ @SUBPROJECT_NAME + @UTY_suffix + '(' ;

-- on boucle sur les tablen (dans tablem) --
  DECLARE C_COLUMN_LOCATION LOCAL CURSOR FOR
		SELECT
		FROM T_UNI_tablen
		INNER JOIN T_UNI_tablem ON UTN_fk_UTM= @UTM_id
		OPEN C_COLUMN_LOCATION
		FETCH NEXT FROM C_COLUMN_LOCATION INTO @UTN_id ;
		WHILE @@FETCH_STATUS=0
		BEGIN

END