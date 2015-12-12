<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookController extends Controller {

    public function __construct() {
        # Put anything here that should happen before any of the other actions
    }

    /**
    * Responds to requests to GET /books
    */
    public function getIndex(Request $request) {

        // Get all the books "owned" by the current logged in users
        // Sort in descending order by id
        $books = \App\Book::where('user_id','=',\Auth::id())->orderBy('id','DESC')->get();

        return view('books.index')->with('books',$books);
    }

    /**
    * Responds to requests to GET /books/edit/{$id}
    */
    public function getEdit($id = null) {

        # Get this book and eager load its tags
        $book = \App\Book::with('tags')->find($id);

        if(is_null($book)) {
            \Session::flash('flash_message','Book not found.');
            return redirect('\books');
        }

        # Get all the possible authors so we can build the authors dropdown in the view
        $authorModel = new \App\Author();
        $authors_for_dropdown = $authorModel->getAuthorsForDropdown();

        # Get all the possible tags so we can include them with checkboxes in the view
        $tagModel = new \App\Tag();
        $tags_for_checkbox = $tagModel->getTagsForCheckboxes();

        /*
        Create a simple array of just the tag names for tags associated with this book;
        will be used in the view to decide which tags should be checked off
        */
        $tags_for_this_book = [];
        foreach($book->tags as $tag) {
            $tags_for_this_book[] = $tag->name;
        }

        return view('books.edit')
            ->with([
                'book' => $book,
                'authors_for_dropdown' => $authors_for_dropdown,
                'tags_for_checkbox' => $tags_for_checkbox,
                'tags_for_this_book' => $tags_for_this_book,
            ]);

    }

    /**
    * Responds to requests to POST /books/edit
    */
    public function postEdit(Request $request) {

        $book = \App\Book::find($request->id);

        $book->title = $request->title;
        $book->author_id = $request->author;
        $book->cover = $request->cover;
        $book->published = $request->published;
        $book->purchase_link = $request->purchase_link;

        $book->save();

        if($request->tags) {
            $tags = $request->tags;
        }
        else {
            $tags = [];
        }
        $book->tags()->sync($tags);

        \Session::flash('flash_message','Your book was updated.');
        return redirect('/books/edit/'.$request->id);

    }

    /**
     * Responds to requests to GET /books/create
     */
    public function getCreate() {

        $authorModel = new \App\Author();
        $authors_for_dropdown = $authorModel->getAuthorsForDropdown();

        # Get all the possible tags so we can include them with checkboxes in the view
        $tagModel = new \App\Tag();
        $tags_for_checkbox = $tagModel->getTagsForCheckboxes();

        return view('books.create')
            ->with('authors_for_dropdown',$authors_for_dropdown)
            ->with('tags_for_checkbox',$tags_for_checkbox);
    }

    /**
     * Responds to requests to POST /books/create
     */
    public function postCreate(Request $request) {

        $this->validate(
            $request,
            [
                'title' => 'required|min:5',
                'cover' => 'required|url',
                'published' => 'required|min:4',
              ]
        );

        # Enter book into the database
        $book = new \App\Book();
        $book->title = $request->title;
        $book->author_id = $request->author;
        $book->user_id = \Auth::id(); # <--- NEW LINE
        $book->cover = $request->cover;
        $book->published = $request->published;
        $book->purchase_link = $request->purchase_link;

        $book->save();

        # Add the tags
        if($request->tags) {
            $tags = $request->tags;
        }
        else {
            $tags = [];
        }
        $book->tags()->sync($tags);

        # Done
        \Session::flash('flash_message','Your book was added!');
        return redirect('/books');
    }

    /**
     * Responds to requests to GET /books/show/{title}
     */
    public function getShow($title = null) {

        return view('books.show')->with('title', $title);

    }

    /**
	*
	*/
    public function getConfirmDelete($book_id) {

        $book = \App\Book::find($book_id);

        return view('books.delete')->with('book', $book);
    }

    /**
	*
	*/
    public function getDoDelete($book_id) {

        $book = \App\Book::find($book_id);

        if(is_null($book)) {
            \Session::flash('flash_message','Book not found.');
            return redirect('\books');
        }

        if($book->tags()) {
            $book->tags()->detach();
        }

        $book->delete();

        \Session::flash('flash_message',$book->title.' was deleted.');

        return redirect('/books');

    }

}
