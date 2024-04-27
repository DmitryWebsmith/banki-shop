<?php

namespace App\Http\Controllers\Images;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchImagesByDateTimeRequest;
use App\Http\Requests\SearchImagesByFileNameRequest;
use App\Http\Requests\UploadFilesRequest;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ImagesController extends Controller
{
    public function showUploadImagesPage(): Response
    {
        return Inertia::render('UploadImages');
    }

    public function showApiInfoPage(): Response
    {
        return Inertia::render('ShowApiInfo');
    }

    public function showImagesPage(): Response
    {
        $images = Image::query()
            ->orderBy('created_at', 'DESC')
            ->get();

        return Inertia::render('ShowImages', ['images' => $this->getImagesData($images) ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $image = Image::query()->findOrFail($id);

        $absolutPathToStorageDir = Storage::disk('public')->path('') . "images/";
        $absolutPathToStorageDir = str_replace('/', DIRECTORY_SEPARATOR, $absolutPathToStorageDir);

        $zipFile = $absolutPathToStorageDir . 'zip/' . $image->name . '.zip';
        $zipFile = str_replace('/', DIRECTORY_SEPARATOR, $zipFile);

        if (file_exists($zipFile)) {
            unlink($zipFile);
        }

        $absolutPathToImage = Storage::disk('public')->path('') . "images/" . $image->name;
        $absolutPathToImage = str_replace('/', DIRECTORY_SEPARATOR, $absolutPathToImage);

        if (file_exists($absolutPathToImage)) {
            unlink($absolutPathToImage);
        }

        $image->delete();

        $images = Image::query()
            ->orderBy('created_at', 'DESC')
            ->get();

        return new JsonResponse(['images' => $this->getImagesData($images)], ResponseAlias::HTTP_OK);
    }

    public function searchImagesByFileName(SearchImagesByFileNameRequest $request): JsonResponse
    {
        $images = Image::query()
            ->where('name', 'LIKE', '%' . $request->post('name') . '%')
            ->orderBy('created_at', 'DESC')
            ->get();

        return new JsonResponse(['images' => $this->getImagesData($images)], ResponseAlias::HTTP_OK);
    }

    public function searchImagesByDateTime(SearchImagesByDateTimeRequest $request): JsonResponse
    {
        $startDate = Carbon::parse($request->post('start_date'));
        $endDate = Carbon::parse($request->post('end_date'));

        if ($startDate > $endDate) {
            $message = 'Конечная дата временного диапазона не может быть меньше начальной даты.';
            return new JsonResponse(['message' => $message], ResponseAlias::HTTP_BAD_REQUEST);
        }

        $images = Image::query()
            ->where('created_at', '>', $startDate)
            ->where('created_at', '<', $endDate)
            ->orderBy('created_at', 'DESC')
            ->get();

        return new JsonResponse(['images' => $this->getImagesData($images)], ResponseAlias::HTTP_OK);
    }

    public function storeImages(UploadFilesRequest $request): JsonResponse
    {
        $images = $request->all()['images'];

        foreach ($images as $image) {
            $file = $image['file'];

            if ($file === null) {
                continue;
            }

            $originalFileName = $file->getClientOriginalName();

            $filename = str_slug(strtolower(pathinfo($originalFileName, PATHINFO_FILENAME)));
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension !== 'jpg' && $extension !== 'jpeg' && $extension !== 'png') {
                $message = 'Допустимые расширения загружаемого файла: jpg, jpeg, png';
                return new JsonResponse(['message' => $message], ResponseAlias::HTTP_BAD_REQUEST);
            }

            $fileSizeInMb = $file->getSize() / 1024 / 1024;

            if ($fileSizeInMb > 1) {
                $message = 'Максимальный размер загружаемого файла не должен превышать 1 Мб';
                return new JsonResponse(['message' => $message], ResponseAlias::HTTP_BAD_REQUEST);
            }

            $uniqueFileName = $filename . '.' . $extension;

            $image = Image::query()
                ->where('name', $uniqueFileName);

            if ($image->exists()) {
                $timestamp = Carbon::now()->timestamp;
                $uniqueFileName = $filename . '_' . $timestamp . '.' . $extension;
            }

            try {
                Storage::disk('local')->put('public/images/' . $uniqueFileName, file_get_contents($file));
            } catch (\Exception $e) {
                return new JsonResponse(['message' => $e->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
            }

            Image::query()
                ->insert(
                    [
                        'name' => $uniqueFileName,
                        'created_at' => Carbon::now(),
                    ]
                );
        }

        return new JsonResponse([], ResponseAlias::HTTP_OK);
    }

    public function downloadImageArchive(int $id): StreamedResponse
    {
        $image = Image::query()->findOrFail($id);

        return Storage::download($this->zipImage($image->name));
    }

    private function getImagesData(Collection $images): array
    {
        $url = config('app.url');

        if (str_contains($url, 'localhost')) {
            $url .= ':8000';
        }

        $url .= '/storage/images/';

        $imagesList = [];

        foreach ($images as $image) {
            $imagesList[] = [
                'id' => $image->id,
                'name' => $image->name,
                'url' => $url . $image->name,
                'created_at' => Carbon::parse($image->created_at)->format('Y-m-d H:i'),
            ];
        }

        return $imagesList;
    }

    private function zipImage(string $imageName): string
    {
        $absolutPathToStorageDir = Storage::disk('public')->path('') . "images/";
        $absolutPathToStorageDir = str_replace('/', DIRECTORY_SEPARATOR, $absolutPathToStorageDir);

        $zipFile = $absolutPathToStorageDir . 'zip/' . $imageName . '.zip';
        $zipFile = str_replace('/', DIRECTORY_SEPARATOR, $zipFile);

        if (file_exists($zipFile)) {
            return "public/images/zip/" . $imageName . '.zip';
        }

        if (!is_dir($absolutPathToStorageDir . 'zip')) {
            mkdir($absolutPathToStorageDir . 'zip', 0755, true);
        }

        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFile($absolutPathToStorageDir.$imageName, $imageName);

        $zip->close();

        return "public/images/zip/" . $imageName . '.zip';
    }
}
