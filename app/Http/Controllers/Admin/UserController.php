<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\ApproveUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Intervention\Image\ImageManagerStatic as Image;


class UserController extends Controller
{

    public function all_users()
    {
        $users = User::where('admin', 0)->get();
        return view('admin.user.list', compact('users'));
    }
    public function active_users()
    {
        $users = User::where('admin', 0)->where('status', 1)->get();
        return view('admin.user.active', compact('users'));
    }
    public function inactive_users()
    {
        $users = User::where('admin', 0)->where('status', 0)->get();
        return view('admin.user.inactive', compact('users'));
    }
    public function admins()
    {
        $users = User::where('admin', 1)->get();
        return view('admin.user.admins', compact('users'));
    }

    public function user_details($id)
    {
        $user_details = User::findOrFail($id);
        return view('admin.user.personal', compact('user_details'));
    }

    public function backDate(Request $request)
    {
        $id = $request->user_id;
        $user = User::findOrFail($id);
        $user->update(['created_at' => $request->created_at]);
        return redirect()->back()->with('success', 'Date updated successfully');
    }

    public function edit_details($id)
    {
        $user_details = User::findOrFail($id);
        return view('admin.user.edit-info', compact('user_details'));
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 1;
        $user->save();
        Notification::send($user, new ApproveUser($user));
        return redirect()->back();
    }
    public function blockUser($id)
    {
        $user = User::findOrFail($id);
        $user->status = 0;
        $user->save();
        return redirect()->back()->with('success', "User Has Been blocked");
    }

    public function delete_user($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', "User Deleted");
    }

    public function create()
    {
        return view('admin.user.add-user');
    }

    public function store_user(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'country' => 'nullable',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'state' => 'nullable',
            'city' => 'nullable',
            'address' => 'nullable',
            'pass' => 'nullable',
            'gender' => 'nullable',
            'm_status' => 'nullable',
            'country_code' => 'required',
            'phone' => 'nullable',
            'dob' => 'nullable',
            'account_type' => 'required',
            'preferred_currency' => 'required',

            'cus_identification' => 'nullable',
            'cus_idnumber' => 'nullable',
            'cus_expiry	' => 'nullable',
            'cus_image	' => 'nullable',

            'occupation' => 'nullable',
            'position' => 'nullable',
            'annual_salary' => 'nullable',
            'office_name' => 'nullable',
            'office_address' => 'nullable',
            'employer_name' => 'nullable',

        ]);

        if ($image = $request->file('avatar')){

            $avatar = $request->file('avatar'); // in here
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(200, 200)->save(public_path('avatars/' . $filename));

//            $input['imagename'] = time().'.'.$image->getClientOriginalExtension();
//            $destinationPath = public_path('/avatars');
//            $image->move($destinationPath, $input['imagename']);
//            Image::make($avatar)->resize(150, 150)->save(public_path('avatars/' . $filename));

            $user = new User();
            $user->first_name = $request->get('first_name');
            $user->last_name = $request->get('last_name');
            $user->email = $request->get('email');
            $user->country_code = $request->get('country_code');
            $user->phone = $request->get('phone');
            $user->title = $request->get('title');
            $user->m_status = $request->get('m_status');
            $user->gender = $request->get('gender');
            $user->country = $request->get('country');
            $user->state = $request->get('state');
            $user->city = $request->get('city');
            $user->address = $request->get('address');
            $user->address_2 = $request->get('address_2');
            $user->zipcode = $request->get('zipcode');
//            $user->account_type = $request->get('account_type');
//            $user->account_type = $request->get('account_type');
            $user->preferred_currency = $request->get('preferred_currency');

            $user->cus_identification = $request->get('cus_identification');
            $user->cus_idnumber = $request->get('cus_idnumber');
            $user->cus_expiry = $request->get('cus_expiry');
            $user->cus_image = $request->get('cus_image');

            $user->occupation = $request->get('occupation');
            $user->position = $request->get('position');
            $user->annual_salary = $request->get('annual_salary');
            $user->office_address = $request->get('office_address');
            $user->office_name = $request->get('office_name');
            $user->employer_name = $request->get('employer_name');

            $user->password = Hash::make($request['password']);
            $user->pass = $request->password;
            $user->avatar = $filename;
            $user->save();
            $this->autoCreate($user->id, $request['account_type']);

        }else{
            $data = $this->getData($request);
            $data['password'] = Hash::make($request['password']);
            $data['pass'] = $request->password;
            $user = User::create($data);
            $this->autoCreate($user->id, $request['account_type']);

        }
        return redirect()->route('admin.users');

    }


    protected function getData(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'country' => 'nullable',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'state' => 'nullable',
            'city' => 'nullable',
            'address' => 'nullable',
            'pass' => 'nullable',
            'gender' => 'nullable',
            'm_status' => 'nullable',
            'phone' => 'nullable',
            'dob' => 'nullable',
            'account_type' => 'nullable',
            'preferred_currency' => 'nullable',

            'cus_identification' => 'nullable',
            'cus_idnumber' => 'nullable',
            'cus_expiry	' => 'nullable',
            'cus_image	' => 'nullable',

            'occupation' => 'nullable',
            'position' => 'nullable',
            'annual_salary' => 'nullable',
            'office_name' => 'nullable',
            'office_address' => 'nullable',
            'employer_name' => 'nullable',
        ];

        return $request->validate($rules);
    }


    public function update_user(Request $request, $id)
    {
        if ($image = $request->file('avatar')){
            $avatar = $request->file('avatar'); // in here
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(200, 200)->save(public_path('avatars/' . $filename));

            $user = User::findOrFail($id);
            $user->update(['avatar' => $filename]);
            $data = $this->getUpdateData($request);
            $user->update($data);
            return redirect()->back()->with('success', 'Profile Updated Successful');
        }
        $user = User::findOrFail($id);
        $data = $this->getUpdateData($request);
        $user->update($data);
        return redirect()->back()->with('success', 'Profile Updated Successful');
    }


    protected function getUpdateData(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'title' => 'nullable',
//            'email' => 'nullable|email|unique:users',
            'country' => 'nullable',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'state' => 'nullable',
            'city' => 'nullable',
            'address' => 'nullable',
            'pass' => 'nullable',

            'gender' => 'nullable',
            'm_status' => 'nullable',
            'country_code' => 'nullable',
            'phone' => 'nullable',
            'dob' => 'nullable',
            'account_type' => 'nullable',
            'preferred_currency' => 'nullable',

            'cus_identification' => 'nullable',
            'cus_idnumber' => 'nullable',
            'cus_expiry	' => 'nullable',
            'cus_image	' => 'nullable',

            'occupation' => 'nullable',
            'position' => 'nullable',
            'annual_salary' => 'nullable',
            'office_name' => 'nullable',
            'office_address' => 'nullable',
            'employer_name' => 'nullable',
        ];

        return $request->validate($rules);
    }



}
