<?php

namespace App\Http\Controllers;

use App\Exceptions\ImagesExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\RolesExceptions;
use Intervention\Image\ImageManagerStatic as ImageManager;
use App\Models\Image;
use App\Models\User;

class ImagesController extends Controller
{
    public $maxSimultaneousLoads = 10;

    public function imageValidator(Request $request)
    {
        return Validator::make(
            $request->all(),
            [
                'image' => 'image|max:2048|mimes:png,jpg,jpeg|required',
                'user_id' => 'numeric',
            ],
            [
                'max' => 'Изображение должно весить меньше :max килобайт',
                'image' => 'Изображение не передано в запросе',
                'mimes' => 'Допустимые форматы изображения: :values'
            ]
        );
    }

    public function uploadImage(Request $request)
    {
        $movePath = public_path('images');

        $uploaded = $request->image;
        $nameWithoutExtension = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
        $imageNamePrefix = substr(md5(time()), -8) . '_';
        $imageName = $imageNamePrefix . $uploaded->getClientOriginalName();
        $imageNameWebp = $imageNamePrefix . $nameWithoutExtension . '.webp';
        $uploaded->move($movePath, $imageName);

        $image = ImageManager::make($movePath . '/' . $imageName);
        $filesizeKb = (int) ($image->filesize() / 1024);

        $path = 'images/' . $imageName;
        $webpPath = 'images/' . $imageNameWebp;

        $image->save(public_path($path), 50);
        $image->save(public_path($webpPath), 100);

        return [
            'path' => $path,
            'webp_path' => $webpPath,
            'size_kb' => $filesizeKb,
            'width' => $image->width(),
            'height' => $image->height(),
            'user_id' => $request->cookie('user')
        ];
    }

    public function handleStoreRequest(Request $request)
    {
        if (!User::hasRight($request->cookie('user'), 'load_image', $request))
            return RolesExceptions::noRightsResponse();

        $arr = $request->images;
        // если пришел массив изображений
        if (is_array($arr)) {
            if (count($arr) > $this->maxSimultaneousLoads)
                return ImagesExceptions::uploadsLimitExceededResponse($this->maxSimultaneousLoads);

            $stored = [];
            $firstError = null;
            foreach ($arr as $image) {
                $subRequestData = array_merge($request->all(), ['image' => $image]);
                $subRequest = Request::create('/api/image/load', 'POST', $subRequestData, $request->cookie());
                $storeOrError = $this->store($subRequest);

                if ($storeOrError instanceof Image)
                    array_push($stored, $storeOrError);
                // отправить в ответ только первую возникшую ошибку
                elseif (empty($firstError)) {
                    $decodedResponse = json_decode($storeOrError->getContent());
                    if (property_exists($decodedResponse, 'error'))
                        $firstError = $decodedResponse->error;
                    elseif (property_exists($decodedResponse, 'errors'))
                        $firstError = $decodedResponse->errors;
                }
            }
            return [
                'images' => $stored,
                'error' => $firstError
            ];
        }
        // если пришло одно изображение
        else
            return $this->store($request);
    }

    public function store(Request $request)
    {
        $validator = $this->imageValidator($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $storeData = $this->uploadImage($request);
        return Image::create($storeData);
    }

    public function update(Request $request, $id)
    {
        if (!User::hasRight($request->cookie('user'), 'update_image', $request))
            return RolesExceptions::noRightsResponse();

        $validator = $this->imageValidator($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $image = Image::find($id);
        if (empty($image))
            return response(['error' => ImagesExceptions::noImage()->getMessage()], 400);

        // удалить старое изображение
        $oldImagePath = public_path() . '/' . $image->path;
        unlink($oldImagePath);

        // загрузить новое и записать в бд
        $storeData = $this->uploadImage($request);
        $image->update($storeData);
        return $image;
    }

    public function delete(Request $request, $id = null)
    {
        if (!User::hasRight($request->cookie('user'), 'delete_image', $request))
            return RolesExceptions::noRightsResponse();

        $image = null;
        if ($id) {
            $image = Image::find($id);
        } else {
            $requestQueries = $request->all();
            if (array_key_exists('path', $requestQueries))
                $image = Image::where('path', $requestQueries['path'])->first();
        }

        return $this->deleteByIdOrImage($image);
    }

    /* используется только из других контроллеров, т.к. не проверяет права пользователя. Подразумевается, что права были проверены ранее в других методах */
    public function deleteByIdOrImage($idOrImage)
    {
        $image = null;
        if (is_numeric($idOrImage))
            $image = Image::find($idOrImage);
        else
            $image = $idOrImage;

        if (empty($image))
            return response(['error' => ImagesExceptions::noImage()->getMessage()], 400);

        $path = public_path() . '/' . $image->path;
        $webpPath = public_path() . '/' . $image->webp_path;
        unlink($path);
        unlink($webpPath);

        $image->delete();

        if (count(scandir(dirname($path))) <= 2) {
            rmdir(dirname($path));
        }

        return ['success' => true];
    }
}
