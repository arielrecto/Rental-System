<?php

namespace App\Actions;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

class UpdateStatusModelPayable
{
    /**
     * Securely finds a model by its class path and ID, then updates its status.
     *
     * @param string $modelPath The fully-qualified class name (e.g., App\Models\Invoice::class)
     * @param int|string $modelId The primary key of the model.
     * @param string $status The new status to set.
     * @return Model The updated model instance.
     */
    public static function execute(string $modelPath, $modelId, string $status): Model
    {
        // --- Security Validation ---
        self::validateModelPath($modelPath);

        // --- Find the Model ---
        // We call the static 'find' method on the validated class string.
        
        /** @var Model $model */
        $model = $modelPath::find($modelId);

        if (!$model) {
            throw new ModelNotFoundException("No record found for {$modelPath} with ID {$modelId}");
        }

        // --- Perform the Update ---
        $model->update([
            'status' => $status
        ]);

        return $model->fresh();
    }

    /**
     * Validates that the given class path is a real, safe-to-use model.
     */
    private static function validateModelPath(string $modelPath): void
    {
        // 1. Check if the class exists at all
        if (!class_exists($modelPath)) {
            throw new InvalidArgumentException("Class does not exist: {$modelPath}");
        }

        // 2. Check if it's an Eloquent Model
        if (!is_subclass_of($modelPath, Model::class)) {
            throw new InvalidArgumentException("Class is not an Eloquent model: {$modelPath}");
        }

       
    }
}