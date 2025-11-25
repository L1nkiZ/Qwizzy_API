## 🚀 Accès à Swagger
Vous pouvez maintenant accéder à votre documentation Swagger à l'adresse : `http://localhost:8000/`

Pour généré un nouveau swagger avec les mise à jour : `docker exec -it qwizzy_app php artisan l5-swagger:generate`

Comment ajouter de la docs aux autres endpoints

 ```php
 <?php
/**
 * @OA\Get(
 *      path="/api/votre-route",
 *      operationId="getNomOperation",
 *      tags={"VotreTag"},
 *      summary="Résumé de l'endpoint",
 *      description="Description détaillée",
 *      @OA\Response(response=200, description="Succès")
 * )
 */
public function nomMethode() {
    // ...
}
 ```

## Accès à DB

Dans l'application postgres du docker :

* Clic droit sur "Servers" (dans le panneau de gauche) → "Register" → "Server..."

* Onglet "General" :

    * Name : Qwizzy DB (ou le nom de votre choix)
    * Onglet "Connection" :
    * Host name/address : db (nom du service Docker, pas "localhost")
    * Port : 5432
    * Maintenance database : qwizzy_api
    * Username : qwizzy_user
    * Password : qwizzy_password
    * Cochez "Save password" > Au besoin 

Note pour Powershell :
docker restart qwizzy_app
docker exec -it qwizzy_app php artisan l5-swagger:generate
docker exec qwizzy_app php artisan migrate:fresh

docker exec -it qwizzy_app vendor/bin/pint //Commande de pour regler les probleme de lint
