<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DominioProducto;
use App\Models\Photo;
use App\Models\Property;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\JpegEncoder;

class UtilsController extends Controller
{
    public function uploadImageProperty(Request $request)
    {
        config(['filesystems.default' => 's3']);


        $validator = Validator::make($request->all(), [
            'images'   => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'property_id' => 'required|integer|exists:properties,id',
        ]);


        // 1. Captura el fallo y devuelve tu respuesta personalizada
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'custom_message' => 'Ocurrieron errores al validar los datos de las imágenes.',
                'validation_errors' => $validator->errors() // Muestra los errores detallados
            ], 422);
        }


        $id = $request->input('property_id');
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['error' => 'No se encontró el modelo para adjuntar el archivo.'], 404);
        }

        foreach ($request->file('images') as $file) {

            try {
                $property->addMedia($file)
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('hotels', 's3');
            } catch (Exception $e) {
                return response()->json(['error' => 'Error al subir la imagen.'], 500);
            }
        }

        // 5. Respuesta
        return response()->json([
            'message' => '¡Imagen de prueba subida con éxito a S3 usando Media Library!',
            'media_id' => 1,
            'file_name' => "OK",
            's3_url' => "URL", // La URL generada de S3 (debería funcionar si la config. es correcta)
        ]);
    }

    public function uploadImageByProperty(Request $request, int $propertyId)
    {

        $property = Property::find($propertyId);

        if (!$property) {
            return response()->json(['error' => 'Propiedad no encontrada.'], 404);
        }

        // 1. Validar archivos
        $fields = ['main_photo_lg', 'main_photo_md', 'main_photo_sm'];
        foreach ($fields as $field) {
            if (!$request->hasFile($field)) {
                return response()->json(['error' => "No se encontró el archivo: $field"], 400);
            }
        }

        // 2. Inicializar ImageManager
        $manager = new ImageManager(new Driver());

        // 3. Definir tamaños (puedes cambiarlos)
        $sizes = [
            'main_photo_lg' => 1920,
            'main_photo_md' => 600,
            'main_photo_sm' => 400,
        ];

        // 4. Procesar y subir cada imagen
        $uploadedPaths = [];
        foreach ($fields as $field) {
            $uploadedFile = $request->file($field);

            // Leer y manipular la imagen
            $image = $manager->read(file_get_contents($uploadedFile->getRealPath()));
            $image->resize($sizes[$field], null, function ($constraint) {
                $constraint->aspectRatio();
            })->sharpen(5);

            // Codificar en JPG con calidad 80
            $encodedImage = $image->encode(new JpegEncoder(quality: 80));

            // Nombre único
            $nameHash = uniqid($field . '_' . time(), true) . '.jpg';
            $rutaS3 = "hotels/" . $property->id . "/" . $nameHash;

            $property->$field = $rutaS3;

            // Subir a S3
            Storage::disk('s3')->put(
                $rutaS3,
                (string) $encodedImage,
                'public'
            );

            $uploadedPaths[$field] = $rutaS3;
        }


        $property->save();

        return response()->json([
            'message' => '¡Imágenes subidas con éxito!',
            'paths' => $uploadedPaths
        ], 200);
    }
}
