<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\JpegEncoder;

class UtilsController extends Controller
{
    public function uploadImageProperty()
    {
        // 1. Configuración y limpieza (OPCIONAL pero útil para pruebas)
        // Asegúrate de usar un disco compatible con Spatie (ej. 's3' o 'public')
        // El 's3' es el que has estado configurando.
        config(['filesystems.default' => 's3']);

        // 2. Simular la creación de un archivo de imagen en memoria/temporal
        // NOTA: Esto no lee un archivo real, sino que crea un archivo falso para la prueba.


        // 3. Obtener el modelo al que adjuntar el archivo (ej. el primer usuario)
        // Reemplaza 'User::first()' con el modelo real que uses (ej. Post::first() o Product::find(1))

        $properties = Property::query('id', '<', 54)->get();

        if (!$properties) {
            return response()->json(['error' => 'No se encontró el modelo para adjuntar el archivo.'], 404);
        }

        foreach ($properties as $property) {
            // $dominioProducto = DominioProducto::query('producto_id', $property->id)->first();

            // $photos = Photo::query('imageable_type', 'App\Models\Dominios_productos')
            //     ->where('imageable_id', $dominioProducto->id)
            //     ->get();

            $photos = [];

            foreach ($photos as $photo) {

                //$new_uuid = (string) Str::uuid();
                $photoPath = public_path("original/" . $photo->filename);

                if (file_exists($photoPath)) {

                    try {

                        $property->addMedia($photoPath)
                            ->usingFileName($photo->filename)
                            ->preservingOriginal()
                            ->toMediaCollection('hotels', 's3');
                    } catch (Exception $e) {
                        dd($e->getMessage());
                    }
                }


                // 4. Adjuntar el archivo usando Spatie Media Library
                // $media = $property
                //     ->addMedia($dummyFile)
                //     ->toMediaCollection('images'); // 'images' es el nombre de la colección
            }
        }


        // 4. Adjuntar el archivo usando Spatie Media Library
        // $media = $model
        //     ->addMedia($dummyFile)
        //     ->toMediaCollection('images'); // 'images' es el nombre de la colección

        // 5. Respuesta
        return response()->json([
            'message' => '¡Imagen de prueba subida con éxito a S3 usando Media Library!',
            'media_id' => 1,
            'file_name' => "FINO",
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
            'main_photo_lg' => 1200,
            'main_photo_md' => 800,
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
            $rutaS3 = "hotels/{$nameHash}";

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
