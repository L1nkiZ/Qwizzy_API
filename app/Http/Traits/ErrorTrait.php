<?php

namespace App\Http\Traits;

trait ErrorTrait
{
    /**
     * Property for generic error response
     */
    protected $error = [
        'error' => true,
        'message' => 'Une erreur est survenue',
    ];

    /**
     * Generate a success response
     *
     * @param  string  $entity  The entity name (e.g., "La difficulté", "Le sujet")
     * @param  string  $action  The action performed (e.g., "créée", "modifiée", "supprimée")
     * @param  mixed|null  $data  Optional data to include in the response
     */
    protected function success(string $entity, string $action, $data = null): array
    {
        $response = [
            'error' => false,
            'message' => "{$entity} a été {$action} avec succès",
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $response;
    }
}
