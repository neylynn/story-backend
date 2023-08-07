<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Story;
use Validator;
use App\Http\Resources\StoryResource;
use Illuminate\Http\JsonResponse;
   
class StoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        if (auth()->user()->role === 'user') {
            $stories = Story::where('status', 'Published')->get();
        }else{
            $stories = Story::all();
        }
        return $this->sendResponse(StoryResource::collection($stories), 'Stories retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'title' => 'required',
            'content' => 'required',
            'status' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input['created_by'] = auth()->user()->role;
        $story = Story::create($input);
   
        return $this->sendResponse(new StoryResource($story), 'Story created successfully.');
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $current_role = auth()->user()->role;
        $story_role = Story::where('id', $id)->first('created_by');

        if ($current_role === 'user' && $story_role->created_by !== 'user') return response()->json(['error' => 'Unauthorized'], 403);

        $story = Story::find($id);
  
        if (is_null($story)) {
            return $this->sendError('Story not found.');
        }
   
        return $this->sendResponse(new StoryResource($story), 'Story retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Story $story): JsonResponse
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'title' => 'required',
            'content' => 'required',
            'status' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $current_role = auth()->user()->role;
        $story_role = Story::where('id', $story->id)->first('created_by');

        if ($current_role === 'user' && $story_role->created_by !== 'user') return response()->json(['error' => 'Unauthorized'], 403);

        $story->title = $input['title'];
        $story->content = $input['content'];
        $story->status = $input['status'];
        $story->save();
   
        return $this->sendResponse(new StoryResource($story), 'Story updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Story $story): JsonResponse
    {
        $current_role = auth()->user()->role;
        $story_role = Story::where('id', $story->id)->first('created_by');

        if ($current_role === 'user' && $story_role->created_by !== 'user') return response()->json(['error' => 'Unauthorized'], 403);

        $story->delete();
   
        return $this->sendResponse([], 'Story deleted successfully.');
    }
}
