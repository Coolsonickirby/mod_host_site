<?php

namespace App\Http\Controllers;

use File;
use App\Models\Items;
use Illuminate\Http\Request;
use Yosymfony\Toml\TomlBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{

    public function showItems(){
        $items = Items::all();

        return view("welcome")->with("items", $items);
    }

    public function showItem($id){
        $item = Items::find($id, "id")->get();

        return view("item")->with("item", $item[0]);
    }

    public function SubmitItem(Request $request){

        $request->validate([
            'type' => 'required|in:mod,skyline_plugin',
            'name' => 'required|max:50',
            'file' => 'required',
            'semver' => 'required|min:5',
            'images.*' => 'image|mimes:jpeg,png,jpg',
        ]);


        return MainController::saveItem($request);
    }

    public static function saveItem(Request $request){
        
        $owner_id = Auth::id();

        $display_name = $request->input("name");
        
        $folder_name = MainController::filter_filename($request->input("name")) . " - {$owner_id}";
        
        $version = $request->input("semver");
        
        $file = $request->file('file');
        
        $filtered_file_name = MainController::filter_filename($request->file('file')->getClientOriginalName());
        
        $file_path = $file->storeAs("public/plugins/{$folder_name}/", $filtered_file_name);
        
        $files_path = public_path() . "\\storage\\plugins\\{$folder_name}\\files";
        
        File::makeDirectory($files_path);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            shell_exec("./tools/tar/bsdtar.exe -x -f \"./storage/plugins/{$folder_name}/{$filtered_file_name}\" -C \"{$files_path}\"");
        } else {
            return shell_exec("tar -x -f \"./storage/plugins/{$folder_name}/{$filtered_file_name}\" -C \"{$files_path}\"");
        }
        
        File::delete(public_path() . "\\storage\\plugins\\{$folder_name}\\$filtered_file_name");
        
        $files_toml = array();
        
        $di = new \RecursiveDirectoryIterator($files_path);
        foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {
            if(!str_contains($filename, "\.")){
                $path = str_replace("\\", "/", substr($filename, strlen($files_path) + 1));
                                
                $server_path = "files/{$path}";

                if($request->input("type") == "mod"){
                    array_push($files_toml, array(
                        "sd:/ultimate/mods/{$folder_name}/{$path}",
                        "files/{$path}"
                    ));
                }else if($request->input("type") == "skyline_plugin"){
                    array_push($files_toml, array(
                        "sd:/atmosphere/contents/01006A800016E000/romfs/skyline/plugins/{$path}",
                        "files/{$path}"
                    ));
                }
            }
        }

        $image_names = array();

        if($request->file('images')){
            foreach ($request->file('images') as $image) {
                $image->store("public/plugins/{$folder_name}/images");
                $image_location = "storage/plugins/{$folder_name}/images/{$image->hashName()}";
                array_push($image_names, $image_location);
            }
        }
        
        
        $server_toml = MainController::generateServerTOML($folder_name, $display_name, $version, $files_toml);
        
        $server_toml_path = public_path() . "\\storage\\plugins\\{$folder_name}\\plugin.toml";
        
        file_put_contents($server_toml_path, $server_toml);

        // return "<h3>Server TOML</h3><pre>" . $server_toml . "</pre><h3>User TOML</h3><pre>" . MainController::generateUserTOML($id) . "</pre>";

        $item = new Items();

        $item->type = $request->input("type");

        $item->name = $display_name;

        $item->folder_name = $folder_name;

        $item->description = $request->input("description");

        $item->version = $version;

        $item->images = serialize($image_names);

        $item->owner_id = $owner_id;

        $item->save();

        return redirect("/");
    }

    public function ownedItems(){
        $owner_id = Auth::id();

        $owned_items = Items::find($owner_id, "owner_id")->get();

        return view("welcome")->with('items', $owned_items);
    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }

    public static function savePlugin(Request $request){

    }

    public static function generateServerTOML($folder_name, $display_name, $version, $files){        
        $tb = new TomlBuilder();

        $file_template = "files = [\n";

        foreach ($files as $file) {
            $file_template = $file_template . "\t{ install_location = \"{$file[0]}\", filename = \"{$file[1]}\" },\n";
        }

        $file_template = $file_template . "]";

        $result = $tb//->addValue('display_name', $display_name)
        ->addValue('name', $folder_name)
        ->addValue('version', $version)->getTomlString();

        $result = $result . $file_template;

        return $result;
    }
    
    public static function generateUserTOML($id){
        $item = Items::find($id, "id")->get()[0];
        
        $tb = new TomlBuilder();

        $result = $tb->addValue('name', $item->folder_name)
        ->addValue('version', $item->version)
        ->addValue('server_ip', env('SERVER_IP', "localhost"))->getTomlString();

        return "<pre>{$result}</pre>";
    }

    public static function filter_filename($name)
    {
        // remove illegal file system characters https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        $name = str_replace(array_merge(
            array_map('chr', range(0, 31)),
            array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
        ), '', $name);
        // maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $name = mb_strcut(pathinfo($name, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($name)) . ($ext ? '.' . $ext : '');
        return $name;
    }
}
