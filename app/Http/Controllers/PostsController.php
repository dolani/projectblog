<?php

namespace App\Http\Controllers;
use Response;
use Illuminate\Http\Request;
use App\Posts;
use App\User;
use App\Http\Requests;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct(){
        $this->middleware('jwt.auth');
    }

    public function index(Request $request)
    {
        //
         $search_term = $request->input('search');
        $limit = $request->input('limit')?$request->input('limit'):2;
 
        if ($search_term)
        {
            $posts = Posts::orderBy('id', 'DESC')->where('title', 'LIKE', "%$search_term%")->with(
            array('User'=>function($query){
                    $query->select('id','name');
                })
                )->select('id', 'author_id','title', 'body', 'slug')->paginate($limit);

                $posts->appends(array(
                    'search' => $search_term,
                    'limit' => $limit
                ));
            }
            else
            {
                $posts = Posts::orderBy('id', 'DESC')->with(
            array('User'=>function($query){
                $query->select('id','name');
            })
            )->select('id', 'author_id','title', 'body', 'slug')->paginate($limit); 
 
            $posts->appends(array(            
                'limit' => $limit
            ));
        }

        return Response::json($this->transformCollection($posts), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
         if(! $request->body or ! $request->author_id){
            return Response::json([
                'error' => [
                    'message' => 'Please Provide Both body and author_id'
                ]
            ], 422);
        }
        $post = Posts::create($request->all());
 
        return Response::json([
                'message' => 'Post Created Succesfully',
                'data' => $this->transform($post)
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $post = Posts::with(
            array('User' => function($query){
                $query->select('id', 'name');
            })
            ) -> find($id);

        if(!$post){
            return Response::json([
                'error'=> [
                    'message' => 'Post does not exist'
                    ]
                ], 404);
        }

        $previous = Posts::where('id', '<', $post->id)->max('id');

        $next = Posts::where('id', '>', $post->id)->min('id');

        return Response::json([
            'previous_post_id'=>$previous,
            'next_post_id'=>$next,
            'data' => $this->transform($post)
            ], 200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        if(! $request->body or ! $request->author_id){
            return Response::json([
                'error' => [
                    'message' => 'Please Provide Both body and user_id'
                ]
            ], 422);
        }
        
        $post = Posts::find($id);
        $post->body = $request->body;
        $post->author_id = $request->author_id;
        $post->save(); 
 
        return Response::json([
                'message' => 'Post Updated Succesfully'
        ]);   //
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
        Posts::destroy($id);
    }

    private function transformCollection($posts){
        $postsArray = $posts->toArray();
        return [
            'total' => $postsArray['total'],
            'per_page' => intval($postsArray['per_page']),
            'current_page' => $postsArray['current_page'],
            'last_page' => $postsArray['last_page'],
            'next_page_url' => $postsArray['next_page_url'],
            'prev_page_url' => $postsArray['prev_page_url'],
            'from' => $postsArray['from'],
            'to' =>$postsArray['to'],
            'data' => array_map([$this, 'transform'], $postsArray['data'])
        ];
    }

    private function transform($post){
        return [
        'post_id' => $post['id'],
        'post_title' => $post['title'],
        'post' => $post['body']
        ];
    }
}
