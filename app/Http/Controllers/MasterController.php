<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuModel;
use App\Models\AdminModel;
use App\Models\BannersModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class MasterController extends Controller
{
    // Check if the admin is logged in
    private function isAuthenticated()
    {
        return session()->has('admin') && session()->get('admin')->token === session('admin_token');
    }

    // Redirect to dashboard if authenticated, otherwise login page
    public function login()
    {
        return $this->isAuthenticated() ? view('/dashboard') : view('login');
    }

    // Fetch model based on 'pagefrom'
    private function getModel($pagefrom)
    {
        return match ($pagefrom) {
            'menu' => MenuModel::class,
            'banners' => BannersModel::class,
                // Add other models as needed
            default => null,
        };
    }

    public function fetch(Request $request)
    {

        try {
            $model = $this->getModel($request->pagefrom);
            if (!$model) {
                throw new \Exception('Invalid model');
            }

            // Fetch data based on 'pagefrom' with optional data
            $data = match ($request->pagefrom) {
                'banners' => ['menuData' => $this->getModel('menu')::all()],
                'service' => ['categoryData' => $this->getModel('service-category')::all()],
                'package' => ['serviceData' => $this->getModel('service')::all()],
                default => [],
            };

            // Add paginated items to the data array
            $data['items'] = $model::orderBy('id', 'desc')->paginate(10);

            return view($request->pagefrom, $data);
        } catch (\Exception $e) {
            //dd($e);
            return view('404');
        }
    }

    public function showMenu()
    {
        $menus = MenuModel::with('children')->whereNull('parent_menu')->orderBy('position')->get();
        return view('menu', compact('menus'));
    }

   
    
    public function uploadImage(Request $request)
    {
        // Log incoming request
        Log::info('Image upload request received', $request->all());

        // Validate the image input
        $request->validate([
            'croppedImage' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if the file is present in the request
        if ($request->hasFile('croppedImage')) {
            // Store the image in the 'uploads' directory in the public disk
            $path = $request->file('croppedImage')->store('uploads', 'public');

            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
            ]);
        }

        // If no file is found, return an error response
        return response()->json([
            'success' => false,
            'message' => 'No image uploaded',
        ], 400);
    }

    public function update(Request $request)
    {
        Log::info('Update request received', $request->all());
        // Validate data
        $rules = $this->getValidationRules($request);
        $validatedData = $request->validate($rules);
        // print_r($validatedData);
        try {
            if (isset($validatedData['scope_work'])) {
                $validatedData['scope_work'] = base64_encode($validatedData['scope_work']);
            }

            $model = $this->getModel($request->pagefrom);
            if (!$model) {
                return response()->json(['success' => false, 'message' => 'Invalid model specified.'], 400);
            }
            if ($request->hasFile('image')) {
                // Store the image and get the file path
                $imagePath = $request->file('image')->store('uploads', 'public');
                $validatedData['image'] = $imagePath; // Save path to the validated data
            }
            // Create or update the record
            if ($request->action === 'Create') {
                $item = $model::create($validatedData);
            } else {
                $item = $model::find($request->id);
                if (!$item) {
                    return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
                }
                $item->update($validatedData);
            }

            return response()->json([
                'success' => true,
                'message' => ucfirst($request->pagefrom) . ' ' . ($request->action === 'Create' ? 'created' : 'updated') . ' successfully.',
                'data' => $item,
                'action' => $request->action,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    // Generate validation rules dynamically
    private function getValidationRules(Request $request)
    {
        $rules = [];
        foreach ($request->all() as $key => $value) {
            $rules[$key] = match ($key) {
                'description' => 'nullable|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'parent_menu' => 'nullable|integer',
                default => 'nullable|string|max:255',
            };
        }
        return $rules;
    }

    public function delete(Request $request)
    {
        try {
            $model = $this->getModel($request->pagefrom);
            $item = $model::findOrFail($request->id);
            $item->delete();

            return response()->json(['success' => true, 'message' => ucfirst($request->pagefrom) . ' deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function adminlogin(Request $request)
    {
        $request->validate(['username' => 'required|string', 'password' => 'required|string']);
        $admin = AdminModel::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            $token = bin2hex(random_bytes(32));
            $admin->update(['token' => $token]);
            session(['admin' => $admin, 'admin_token' => $token]);

            return response()->json(['success' => true, 'message' => 'Login successful!']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    }
    public function dashboard()
    {
        return view('dashboard');
    }
    public function logout()
    {
        if ($this->isAuthenticated()) {
            session('admin')->update(['token' => null]);
            session()->forget(['admin', 'admin_token']);
            return view('login');
        }

        return response()->json(['success' => false, 'message' => 'No user is currently logged in.']);
    }
}
