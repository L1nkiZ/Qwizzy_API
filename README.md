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
