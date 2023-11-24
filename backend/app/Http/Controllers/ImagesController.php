<?php

namespace App\Http\Controllers;

use App\Exceptions\ImagesExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\RolesExceptions;
use Intervention\Image\ImageManagerStatic as ImageManager;
use App\Models\Image;
use App\Models\User;
use App\Filters\ImagesFilter;

class ImagesController extends Controller
{
    public $maxSimultaneousLoads = 10;

    /* такие суффиксы могут быть присвоены изображениям. При удалении изображений через deleteByIdOrImage, также удалит изображения с этими суффиксами, если они есть */
    public $supportsSuffixes = [
        '195x195',
        '430x430'
    ];

    public function getGallery(ImagesFilter $queryFilter)
    {
        $limit = $queryFilter->request->get('limit') ?? 10;
        $offset = $queryFilter->request->get('offset') ?? null;

        $galleryQuery = Image::filter($queryFilter);

        $uploaders = User::galleryUploaders()->get();
        $count = $galleryQuery->count();

        $galleryQuery->forGallery($queryFilter->request)
            ->offsetLimit($limit, $offset);

        return [
            'result' => $galleryQuery->get(),
            'total_count' => $count,
            'all_uploaders' => $uploaders
        ];
    }

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
        $user = User::authenticate($request);
        if (empty($user))
            return;

        $movePathRelative = 'images/gallery/' . $user->id;
        $movePath = public_path($movePathRelative) . '/';
        if (!is_dir($movePath))
            mkdir($movePath, 0777, true);
        $warning = null;

        $canUploadToSubpath = User::hasRight(
            $request->cookie('user'),
            'upload_image_to_subpath',
            $request
        );

        $uploaded = $request->image;
        $nameWithoutExtension = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
        $imageNamePrefix = substr(md5(time()), -8) . '_';
        $imageName = $imageNamePrefix . $uploaded->getClientOriginalName();
        $imageNameWebp = $imageNamePrefix . $nameWithoutExtension . '.webp';
        $uploaded->move($movePath, $imageName);

        $image = ImageManager::make($movePath . $imageName);
        $filesizeKb = (int) ($image->filesize() / 1024);

        $path
            = $webpPath = $movePathRelative . '/';

        // попытка загрузить в subpath, если у пользователя есть на это права и если это не запрещенный путь из Image::$forbiddenPaths
        if ($request->subpath && $canUploadToSubpath) {
            $subpath = 'images/' . $request->subpath;
            if (!preg_match('/\/$/', $subpath))
                $subpath .= '/';
            if (Image::isForbiddenSubpath($subpath))
                $warning = 'Невозможно добавить изображение в этот каталог';
            else {
                $path = $subpath . $imageName;
                $webpPath = $subpath . $imageNameWebp;
            }
        } else {
            $warning = 'У вас нет прав для добавления изображения в этот каталог. Оно будет сохранено по пути ' . $movePathRelative;
            $path .= $imageName;
            $webpPath .= $imageNameWebp;
        }

        $image->save(public_path($path), 50);
        $image->save(public_path($webpPath), 100);

        return [
            'path' => dirname($path) . '/',
            'name' => $imageNamePrefix . $nameWithoutExtension,
            'original_name' => $uploaded->getClientOriginalName(),
            'extension' => $uploaded->getClientOriginalExtension(),
            'size_kb' => $filesizeKb,
            'width' => $image->width(),
            'height' => $image->height(),
            'user_id' => $request->cookie('user'),
            'warning' => $warning
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
                    if (is_array($storeOrError)) {
                        if (array_key_exists('error', $storeOrError))
                            $firstError = $storeOrError['error'];
                    }
                }
            }
            return [
                'result' => $stored,
                'error' => $firstError
            ];
        }
        // если пришло одно изображение
        else
            return $this->store($request);
    }

    public function store(Request $request, $returnErorsAsArray = false)
    {
        $validator = $this->imageValidator($request);
        if ($validator->fails()) {
            if ($returnErorsAsArray)
                ['errors' => $validator->errors(), 'code' => 400];
            else
                return response(['errors' => $validator->errors()], 400);
        }

        $storeData = $this->uploadImage($request);
        $image = Image::create($storeData);

        $returnImage = Image::forGallery()->where('images.id', $image->id)->first();
        if ($storeData['warning'])
            $returnImage->warning = $storeData['warning'];
        return $returnImage;
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
        $origExtPath = public_path() . '/' . $image->path . $image->name . '.' . $image->extension;
        $webpPath = public_path() . '/' . $image->path . $image->name . '.webp';
        if (file_exists($origExtPath))
            unlink($origExtPath);
        if (file_exists($webpPath))
            unlink($webpPath);

        // загрузить новое и записать в бд
        $storeData = $this->uploadImage($request);
        $image->update($storeData);
        if ($storeData['warning'])
            $image->warning = $storeData['warning'];
        return Image::forGallery()->where('images.id', $image->id)->first();
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
            if (array_key_exists('idsList', $requestQueries)) {
                $images = Image::whereIn('id', $requestQueries['idsList'])->get();
                foreach ($images as $imageModel) {
                    if (empty($imageModel))
                        continue;

                    $this->deleteByIdOrImage($imageModel);
                }

                return ['success' => true];
            }
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

        $path = public_path($image->path . $image->name);
        $pathToOrigExt = $path . '.' . $image->extension;
        $pathToWebp = $path . '.webp';
        if (file_exists($pathToOrigExt))
            unlink($pathToOrigExt);
        if (file_exists($pathToWebp))
            unlink($pathToWebp);

        foreach ($this->supportsSuffixes as $suffix) {
            $pathToSuffixed = public_path(
                $image->path . $image->name . '_' . $suffix . '.' . $image->extension
            );
            $pathToSuffixedWebp = public_path(
                $image->path . $image->name . '_' . $suffix . '.webp'
            );

            if (file_exists($pathToSuffixed))
                unlink($pathToSuffixed);
            if (file_exists($pathToSuffixedWebp))
                unlink($pathToSuffixedWebp);
        }

        $image->delete();

        if (is_dir(dirname($path))) {
            if (count(scandir(dirname($path))) <= 2) {
                rmdir(dirname($path));
            }
        }

        return ['success' => true];
    }

    /* если передать $request->tag == false, тег будет удален */
    public function tagImages(Request $request)
    {
        if (!User::hasRight($request->cookie('user'), 'tag_image'))
            return RolesExceptions::noRightsResponse();

        $tag = $request->tag;
        if (!is_string($tag))
            $tag = null;

        $idsList = $request->images;
        if (!is_array($idsList))
            return response(['error' => 'Не переданы изображения'], 400);

        Image::whereIn('images.id', $idsList)->update(['tag' => $tag]);
        return ['success' => true];
    }
}
