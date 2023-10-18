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
        $subpath = $request->subpath ?? '';
        $movePath = $subpath
            ? public_path('images') . '/' . $subpath
            : public_path('images');

        $uploaded = $request->image;
        $imageName = substr(md5(time()), -8) . '_' . $uploaded->getClientOriginalName();
        $uploaded->move($movePath, $imageName);

        $image = ImageManager::make($movePath . '/' . $imageName);
        $filesizeKb = (int) ($image->filesize() / 1024);

        $path = 'images/';
        if ($subpath)
            $path .= $subpath . '/';
        $path .= $imageName;

        return [
            'path' => $path,
            'size_kb' => $filesizeKb,
            'width' => $image->width(),
            'height' => $image->height(),
            'user_id' => $request->cookie('user')
        ];
    }

    /* можно указать subpath в $request, чтобы поместить в public/images/<subpath>/<image>. subpath = name или subpath = name/othername. Это же касается и update()
     */
    public function store(Request $request)
    {
        if (!User::hasRight($request->cookie('user'), 'load_image'))
            return RolesExceptions::noRightsResponse();

        $validator = $this->imageValidator($request);
        if ($validator->fails())
            return response(['errors' => $validator->errors()], 400);

        $storeData = $this->uploadImage($request);
        return Image::create($storeData);
    }

    public function update(Request $request, $id)
    {
        if (!User::hasRight($request->cookie('user'), 'update_role'))
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

    public function delete(Request $request, $id)
    {
        if (!User::hasRight($request->cookie('user'), 'update_role'))
            return RolesExceptions::noRightsResponse();

        $image = Image::find($id);
        if (empty($image))
            return response(['error' => ImagesExceptions::noImage()->getMessage()], 400);

        $path = public_path() . '/' . $image->path;
        unlink($path);

        $image->delete();

        return ['success' => true];
    }
}