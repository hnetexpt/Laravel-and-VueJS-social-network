<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use App\GroupMembers;
use DB;
use Auth;
class GroupController extends Controller
{

    public function index(Request $request)
    {
        $groups = DB::table('groups')
          ->where('visibility', '=', 'public');


        if ($request->has('group_id') && $request->group_id > 0) {
            $groups->where('groups.id', '=', $request->group_id);
        }
        if ($request->has('name')) {
            $groups->where('groups.name', '=', $request->name);
        }
        if ($request->has('search')) {
            $groups->where('groups.name', 'like', "%".$request->search."%")->orwhere('groups.description', 'like', "%".$request->search."%");
        }

        if ($request->has('limit') && $request->limit <= 100) {
            $groups->limit($request->limit);
        } else {
            $groups->limit(15);
        }



        return response()->json([

           'groups' => $groups->get(),
       ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $group=new Group();
        $group->name=$request->name;
        $group->description=$request->description;
        $group->avatar=$request->avatar;
        $group->background="";
        $group->save();


        $members=new GroupMembers();
        $members->user_id=Auth::user()->id;
        $members->group_id=$group->id;
        $members->status="confirmed";
        $members->is_moderator=1;
        $members->save();
        return response()->json([

           'groups' => $group,
       ]);
    }

    public function join(Request $request, $id)
    {
        $membership=new GroupMembers();
        $membership->user_id=Auth::user()->id;
        $membership->group_id=$id;
        $membership->status="awaiting";
        $membership->is_moderator=0;
        $membership->save();

        return response()->json([

           'membership' => $membership,
       ]);
    }

    public function leave(Request $request, $id)
    {
        $membership= DB::table('group_members')->where([
            ['group_id', '=', $id],
            ['user_id', '=', Auth::user()->id],
        ])->delete();

        return response()->json([

           'membership' => $membership,
       ]);
    }

    public function membership(Request $request, $id)
    {
      $moderators= DB::table('group_members')->where([
          ['group_id', '=', $id],
          ['is_moderator',"=", 1]

      ])->select('name', 'avatar')->
      join('users', 'users.id', '=', 'group_members.user_id')->get();

      $awaiting= DB::table('group_members')->where([
          ['group_id', '=', $id],
          ['status' ,"=", 'awaiting']

      ])->select('name', 'avatar')->
      join('users', 'users.id', '=', 'group_members.user_id')->get();


      $members= DB::table('group_members')->where([
          ['group_id', '=', $id],
          ['status' ,"=", 'confirmed']

      ])->select('name', 'avatar')->
      join('users', 'users.id', '=', 'group_members.user_id')->get();

        return response()->json([

           'moderators' => $moderators,
           'pending' => $awaiting,
           'members' => $members
       ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
