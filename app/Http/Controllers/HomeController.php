<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Memo;
use App\Models\Tag;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
         //ログインしているユーザー取得
         $user = \Auth::user();
        //メモ一覧取得
        //全部取得したいならMemo::get()で全部取れる
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('home', compact('user', 'memos'));
    }
    public function create()
    {
        //ログインしているユーザー取得
        $user = \Auth::user();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('create', COMPACT('user', 'memos'));
    }
    public function store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す

        //先にタグをインサート
        $tag_id = Tag::insertGetId([
            'name' => $data['tag'], 
            'user_id' => $data['user_id'],
        ]);
        
        //タグのIDが判明する
        // タグIDをmemosテーブルに入れてあげる
        $memo_id = Memo::insertGetId([
            'content' => $data['content'],
             'user_id' => $data['user_id'], 
             'tag_id' => $tag_id,
             'status' => 1
        ]);
        
        // リダイレクト処理
        return redirect()->route('home');
    }
    public function edit($id){
        // 該当するIDのメモをデータベースから取得
        $user = \Auth::user();
        $memo = Memo::where('status', 1)->where('id', $id)->where('user_id', $user['id'])
          ->first();
        //    dd($memo);
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        //取得したメモをViewに渡す
        return view('edit',compact('memo', 'user','memos'));
    }
    public function update(Request $request,$id)
    {
        $inputs = $request->all();
        Memo::where('id', $id)->update(['content' => $inputs['content']]);
        //  // リダイレクト処理
         return redirect()->route('home');
    }
}
