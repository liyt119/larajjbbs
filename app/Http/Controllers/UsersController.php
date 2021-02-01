<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function __construct()
    {
        //第一个中间件名称，第二个是要进行过滤的动作
        $this->middleware('auth',['except'=>['show']]);
    }

    public function show(User $user)
    {
    	return view('users.show',compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update',$user);
        return view('users.edit',compact('user'));
    }

//UserRequest表单请求验证
     public function update(UserRequest $request, ImageUploadHandler $uploader, User $user){
        $this->authorize('update',$user);
        $data = $request->all();
        //dd($data);
        if($request->avatar){
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 300);
            if($result){
                $data['avatar'] = $result['path'];
            }else{
                //上传有错误  withErrors可以携带回错误信
                return back()->withErrors(['上传图片格式只支持png, jpg, gif, jpeg这四种格式']);
            }
        }
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功');
    }
}
