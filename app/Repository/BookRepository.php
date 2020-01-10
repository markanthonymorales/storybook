<?php

namespace App\Repository;
use Storage;
use Auth;
use PDF;

use Illuminate\Filesystem\Filesystem;

use App\Helper\TPEpubCreator;

use App\User;
use App\StoryShare;
use App\Book;
use App\BookChapter;
use App\BookChapterPage;

use App\Repository\RecentViewedRepository;
use App\Repository\StoryRepository;

use App\Http\Resources\GlobalResource;
class BookRepository{

	public static function setData($request)
    {
    	$book = new Book;
    	$user = Auth::user();

        $getbook = $book->create($request->only([
            'title', 'content', 'description', 
            'price', 'original_price', 'markup_price', 'ebook_price', 'ebook_markup_price', 
            'from_date', 'to_date', 'book_date', 'status_type', 'is_save_as_draft', 'total_page'
        ]));

        $order_num = 0;
        if(!$getbook->getKey())
            return $getBook;

        foreach ($request->get('chapters') as $order_num => $chapter) {
            $getChapter = $getbook->chapters()->create([
                'book_id' => $getbook->getKey(),
                'story_id' => $chapter['id'], 
                'is_file_type' => $chapter['is_file_type'],
                'title' => $chapter['title'],
                'file_url' => $chapter['file_url'],
                'position' => $order_num+=1,
                'total_page' => $chapter['total_page'],
                'colored_index' => $chapter['colored_index'],
            ]);

            if(!$getChapter->getKey())
                continue;

            foreach ($chapter['pages'] as $p => $page) {
                $getChapter->pages()->create([
                    'book_chapter_id' => $getChapter->getKey(),
                    'content' => $page['content'],
                    'is_colored' => $page['is_colored']?1:0,
                ]);
            }
        }

        $getbook->update([
            'user_id' => $user->id,
            'tags' => implode(',', $request->tags),
            'is_deleted' => false,
        ]);

    	return $getbook;
    }

    public static function updateData($request, $id, &$getChaptersId)
    {

        $book = Book::find($id);

        foreach ($request->chapters as $order_num => $chapter) {

            // check if book chapter is exists
            $story_id = $chapter['id'];
            if(isset($chapter['story_id']))
                $story_id = $chapter['story_id'];

            $bookChapter = $book->chapters()->where([ 'story_id' => $story_id ]);
            if($bookChapter->exists()){

                // update
                $bookChapter = $bookChapter->firstOrFail();
                array_push($getChaptersId, $bookChapter->id);

                $bookChapter->update([
                    'position' => $order_num+=1
                ]);

                continue;
            }

            // if not exists then lets create book chapters
            $bookChapter = $book->chapters()->create([
                'book_id' => $request->id,
                'story_id' => $chapter['id'], 
                'is_file_type' => $chapter['is_file_type'],
                'title' => $chapter['title'],
                'file_url' => $chapter['file_url'],
                'position' => $order_num+=1,
                'total_page' => $chapter['total_page'],
                'colored_index' => $chapter['colored_index'],

            ]);

            array_push($getChaptersId, $bookChapter->id);

            foreach ($chapter['pages'] as $p => $page) {
                $bookChapter->pages()->create([
                    'book_chapter_id' => $bookChapter->id,
                    'content' => $page['content'],
                    'is_colored' => $page['is_colored']?1:0,
                ]);

            }
        }

    	$book->update([
			'title' => $request->title,
			'content' => $request->content,
            'description' => $request->description,
			'tags' => implode(',', $request->tags),

            'price' => $request->price,
            'original_price' => $request->original_price,
            'markup_price' => $request->markup_price,
            'ebook_price' => $request->ebook_price,
            'ebook_markup_price' => $request->ebook_markup_price,

            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'book_date' => $request->book_date,
            
            'total_page' => $request->total_page,
			// 'colored_index' => $request->colored_index,

			'status_type' => $request->status_type,            
			'is_save_as_draft' => $request->is_save_as_draft,
			'is_deleted' => false
    	]);

    	return $book;
    }

	public static function getBook($id, $user)
    {
        $book = Book::where('id', $id)->active()->first();

        if(!$book)
            abort(404);

        if(!self::hasAccess($book, $user))
            abort(404);

        
        $getBook = RecentViewedRepository::collectDataToArray($book);

        $countPage = 0;
        foreach($book->chapters as $c => $chapter){
        	
            $title = '<legend style="line-height: 30; text-align: center; margin-bottom: 25px; font-weight: 700;">'.$chapter->title.'</legend>';
            
            $pageNumber = $countPage;
            $pageNumber++;
            array_push($getBook['pages'], [
                'id' => 0,
                'is_cover' => false,
                'content' => '<div style="position: relative; overflow: hidden; height: -webkit-fill-available;">'.$title.'</div>',
                'page_number' => $pageNumber
            ]);

            if($chapter->is_file_type){
                $pageNumber++;
                array_push($getBook['pages'], [
                    'id' => 0,
                    'is_cover' => false,
                    'content' => '<div class="pdf-watermark"><span>PDF File</span></div>',
                    'page_number' => $pageNumber
                ]);
            }

            foreach ($chapter->pages as $p => $page) {
                $pageNumber++;
            	array_push($getBook['pages'], [
	                'id' => $page->id,
                    'is_cover' => false,
	                'content' => '<div style="position: relative; overflow: hidden; height: -webkit-fill-available;">'.$page->content.'</div>',
                    'page_number' => $pageNumber
	            ]);
                $title = '';
            }
            $countPage += count($chapter->pages);
        }

        array_unshift($getBook['pages'], [
            'id' => 0,
            'is_cover' => false,
            'content' => '<div class="table-of-content">'.$book->content.'</div>'
        ]);

        // get front and back cover
        self::getCover($book->user_id, $book->id, 'front', $hasFrontCover, $frontCover);
        if($hasFrontCover){
            array_unshift($getBook['pages'], [
                'id' => 0,
                'is_cover' => true,
                'content' => 'url('.$frontCover.')'
            ]);
        }

        self::getCover($book->user_id, $book->id, 'back', $hasBackCover, $backCover);
        if($hasBackCover){
            array_push($getBook['pages'], [
                'id' => 0,
                'is_cover' => true,
                'content' => 'url('.$backCover.')'
            ]);
        }
        
        $getBook['shared_to'] = self::getEmails($book, false, $getBook);
        if(!$book->is_save_as_draft){
            $getBook['is_file_type'] = true;
            $getBook['file_url'] = '/storage/pdf/'.$book->id.'/'.$book->title.'.pdf';
        }

        return $getBook;
    }

    public static function getMyBooks($user_id, $model){
        return $model->active()->where('user_id', $user_id);
    }

    public static function getPublishBooks($request){
        $user = Auth::user();

        $books = Book::where('is_save_as_draft', '=', 0)->active();

        if($request->title != '')
            $books->where('title','like', '%'.$request->title.'%');

        if($request->filter != 'all')
            $books->where('status_type','=', $request->filter);
        $books->where('status_type','!=', 'private');


        $perPage = $request->page_size;

        if ($request->current_page == 1) {
            $skip = 0;
        }else
            $skip = $perPage * ($request->current_page - 1);

        $total = $books->count();

        $books->skip($skip)
        ->take($perPage);

        $books = $books->orderBy('id', 'DESC')->get();

        $bookToArray = [];
        foreach ($books as $key => $book) {
            $hasFrontCover = null;
            $frontCover = null;
            self::getCover($book->user_id, $book->id, 'front', $hasFrontCover, $frontCover);

            $bookToArray[$key] = GlobalResource::toArray($book, config('properties.publish'));
            $bookToArray[$key]['tags'] = explode(',', $bookToArray[$key]['tags']);
            $bookToArray[$key]['cover'] = $frontCover;

            $has_access = self::hasAccess($book, $user);

            $bookToArray[$key]['has_access'] = $has_access;
        }

        return [
            'total' => $total,
            'current_page' => $request->current_page,
            'page_size' => $request->page_size,
            'books' => $bookToArray
        ];
    }

    public static function hasAccess($book, $user)
    {
        if(!$user)
            return false;

        if($book->user_id == $user->id)
            return true;

        $bool = false;
        foreach ($user->carts as $key => $cart) {
            if($bool)
                break;
            
            $bool = $cart->hasItem(['product_id' => $book->id]);
        }
        return $bool;
    }

    public static function getBookFromCart($user)
    {
        $product_ids = [];
        foreach ($user->carts as $c => $cart) {
            foreach ($cart->items as $i => $item) {
                $product_ids[] = $item->product_id;
            }
        }

        $books = new Book;

        return $product_ids?$books->active()->whereIN('id', $product_ids):null;
    }

    public static function filterBooks($request, $books){
        

        if($request->has('title') 
            || $request->has('keyword') 
            || $request->has('month') 
            || $request->has('year')){

            if($request->has('title'))
                $books->where('books.title', 'like', '%'.$request->title.'%');
            
            if($request->has('keyword'))
                $books->where('books.tags', 'like', '%'.$request->keyword.'%');

            if($request->has('month')){
                if(is_array($request->month)){
                    $books
                    ->whereRaw('MONTH(books.book_date) >= '.$request->month[0])
                    ->whereRaw('MONTH(books.book_date) <= '.$request->month[1]);
                }else
                    $books->whereRaw('MONTH(books.book_date) = '.$request->month);
            }

            if($request->has('year')){
                if(is_array($request->year)){
                    $books
                    ->whereRaw('YEAR(books.book_date) >= '.$request->year[0])
                    ->whereRaw('YEAR(books.book_date) <= '.$request->year[1]);
                }else
                    $books->whereRaw('YEAR(books.book_date) = '.$request->year);
            }
        }
        return $books;
    }

    public static function recentlyViewedBook($user_id)
    {
        $recentViewed = RecentViewedRepository::isExists($user_id, '', 'books', new Book);
        $recentBook = [];
        if($recentViewed->exists()){
            $books = $recentViewed->select(['books.*', 'recent_viewed.updated_at'])
            ->distinct('books.id')
            ->latest('recent_viewed.updated_at')
            ->take(3)
            ->get();

            foreach($books as $book){

                $frontCover = '';
                $hasFrontCover = false;
                self::getCover($book->user_id, $book->id, 'front', $hasFrontCover, $frontCover);
                if(!$hasFrontCover)
                    $frontCover = '';

                $recentBook[] = RecentViewedRepository::collectMultiDataToArray($book, $user_id, $frontCover);
            }
        }
        
        return $recentBook;
    }

    private static function getCover($user_id = null, $book_id = null, $type = 'front', &$has, &$var)
    {
        $source = 'images/book/'.$user_id.'/'.$book_id.'/';

        $path = 'public/'.$source.$type.'Cover.png';
        $destinationPath = public_path('storage/'.$source.'thumbnail');

        if(\File::exists($destinationPath))
            $path = 'public/'.$source.'thumbnail/'.$type.'Cover.png';

        $has = Storage::has($path);
        $var = Storage::url($path);
    }

    public static function regenerateSpineCover($book, $type = 'spine', $rename = 'copy-spine', $widthByType = 565, $heightByType = 842)
    {
        $widthByType = intval(round( $widthByType / 2.54 * 96, 0));
        if($widthByType < 0)
            return false;
        
        $source = 'images/book/'.$book->user_id.'/'.$book->id.'/';

        $path = 'public/'.$source.$type.'Cover.png';
        $destinationPath = public_path('storage/'.$source.'thumbnail');

        if($hasDesination = \File::exists($destinationPath))
            $path = 'public/'.$source.'thumbnail/'.$type.'Cover.png';

        $url = 'storage/'.$source.($hasDesination?'thumbnail/':'');

        if(!Storage::has($path))
            return false; 

        $imagename = $rename.'Cover.png';

        $img = \Image::make($url.'/'.$type.'Cover.png');
        $img->orientate();

        $destinationPath = public_path($url);
        
        if(Storage::has('public/'.$source.($hasDesination?'thumbnail/':'').$rename.'Cover.png'))
            Storage::delete('public/'.$source.($hasDesination?'thumbnail/':'').$rename.'Cover.png');

        $img->fit($widthByType, $heightByType, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->resizeCanvas($widthByType, $heightByType, 'center', false, '#ffffff');
        return $img->save($destinationPath.$imagename);
    }

	public static function convertToArray($book, $isEmailAsObject = false)
    {
        self::getCover($book->user_id, $book->id, 'front', $hasFrontCover, $frontCover);
        self::getCover($book->user_id, $book->id, 'original-copy-spine', $hasSpineCover, $spineCover);
        self::getCover($book->user_id, $book->id, 'back', $hasBackCover, $backCover);

        $bookToArray = GlobalResource::toArray($book, config('properties.book'));
        $bookToArray['shared_to'] = self::getEmails($book, $isEmailAsObject, $bookToArray);;

        $bookToArray['tags'] = $bookToArray['tags']?explode(',', $bookToArray['tags']):[];

        $bookToArray['front_cover'] = $frontCover;
        $bookToArray['has_front_cover'] = $hasFrontCover;

        $bookToArray['spine_cover'] = $spineCover;
        $bookToArray['has_spine_cover'] = $hasSpineCover;

        $bookToArray['back_cover'] = $backCover;
        $bookToArray['has_back_cover'] = $hasBackCover;
        return $bookToArray;
    }

    public static function getEmails($book, $isEmailAsObject, &$bookToArray)
    {
        $getEmail = [];
        foreach($book->chapters as $c => $chapter){
            if(!$chapter->stories)
                continue;

            foreach ($chapter->stories->shares as $s => $share) {
                if(!isset($share->user))
                    continue;

                if(!$isEmailAsObject && in_array($share->user->id.':'.$share->user->email, $getEmail))
                    continue;

                if($isEmailAsObject){

                    $bool = false;
                    foreach ($getEmail as $key => $value){
                        if(strpos($value['email'], $share->user->email) !== false)
                            $bool = true;
                    }

                    if($bool)
                        continue;

                    $data = [
                        'id' => $share->user->id,
                        'email' => $share->user->email,
                        'is_allow_edit' => $share->is_allow_edit,
                    ];

                    array_push($bookToArray['chapters'][$c]['shared_to'], $data);
                    array_push($getEmail, $data);
                    continue;
                }

                array_push($getEmail, $share->user->id.':'.$share->user->email);
            }

            if($chapter->stories->shared_to == '')
                continue;

            $shared_to = explode(',', $chapter->stories->shared_to);
            foreach ($shared_to as $s => $email) {
                if(in_array($email, $getEmail))
                    continue;

                $bool = false;
                foreach ($getEmail as $key => $value){
                    if($isEmailAsObject)
                        if(strpos($value['email'], $email) !== false)
                            $bool = true;
                        
                    if(!$isEmailAsObject)
                        if(strpos($value, $email) !== false)
                            $bool = true;
                }

                if($bool)
                    continue;

                if($isEmailAsObject){
                    $data = [
                        'id' => null,
                        'email' => $email,
                        'is_allow_edit' => false,
                    ];

                    array_push($bookToArray['chapters'][$c]['shared_to'], $data);
                    array_push($getEmail, $data);
                    continue;
                }

                array_push($getEmail, $email);
            }
        }

        return $getEmail;
    }

    public static function cleanChapter($newIds, $book_id){
    	$chapters = BookChapter::whereNotIn('id', $newIds)
    	->where('book_id', $book_id);
    	foreach ($chapters as $key => $chapter) {
    		BookChapterPage::where('book_chapter_id', $chapter->id)->delete();
    	}
    	return $chapters->delete();
    }

    public static function getBookById($user_id = null, $id = null, $isFree = true){
        if(!$id)
            return;

        $book = Book::where('id','=',$id);
        
        if($isFree)
            $book->where('is_save_as_draft','=',0);

        if($isFree && !$user_id)
            $book->where('status_type','=','free');
        
        if(!$book->exists())
            return;
        $book = $book->firstOrFail();
        return self::convertToArray($book);
    }

    public static function generateEPub($id, $book)
    {
        try {
            if(!$book)
                return;

            $fileName = 'generated_epub/'.$id.'/';
            $fileName .= preg_replace("/[\s-]/", "_",strtolower($book['title'])).'.epub';

            // Check file if already exists
            if(is_file($fileName)){

                // remove created folder on storage temp_folder
                $filesystem = new Filesystem;
                $filesystem->deleteDirectory(storage_path('/app/public/temp_folder/'));
                $filesystem->cleanDirectory(storage_path('/app/public/temp_folder/'));
                Storage::deleteDirectory(storage_path('/app/public/temp_folder/'));

                $file = public_path($fileName);
                return $file;
            }

            // Initialize Epub Creator
            $epub = new TPEpubCreator();

            // Temp folder and file name
            $epub->temp_folder = 'public/temp_folder/';
            $epub->epub_file = $fileName;
            $epub->title = $book['title'];
            $epub->creator = $book['author'];

            // Add book front cover path
            $path = 'public/images/book/'.$book['user_id'].'/'.$book['id'].'/thumbnail/';
            if(!Storage::has($path.'frontCover.png'))
                $path = 'public/images/book/'.$book['user_id'].'/'.$book['id'].'/';
            
            if($book['has_front_cover']){
                $epub->AddImage($path.'frontCover.png', 'image/png', true);
            }

            // Add table of content
            if($book['content']){
                $epub->AddPage('<div class="table-of-content">'.$book['content'].'</div>' , false, 'Table of Content');
            }

            // Add pages content directly
            foreach ($book['chapters'] as $c => $chapter) {
                $content = '<div class="chapter">';
                $title = $chapter['title'];
                foreach ($chapter['pages'] as $p => $page) {
                    $content .= '<div class="page ck ck-content">'.$page['content'].'</div>';
                }

                $content = str_replace("<br>","<br />",$content);
                $content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', "$1</img>", $content);
                $found = preg_match_all('/(src)=("[^"]*")/i',$content, $results);

                if($found){
                    if(!empty($results[2])){
                        foreach ($results[2] as $key => $result) {

                            $oldSrc = str_replace('"', '', $result);
                            if(\File::exists(public_path().$oldSrc)){
                               $newSrc = 'images/image'.$key.'.jpg';
                            
                                $epub->content_images[] = [
                                    'oldSrc' => public_path().$oldSrc,
                                    'newSrc' => public_path().'/storage/temp_folder/'.$epub->uuid.'/OEBPS/'.$newSrc
                                ];

                                $content = str_replace($oldSrc,$newSrc,$content); 
                            }
                            
                        }
                    }
                }

                $epub->AddPage($content.'</div>' , false, $title);
            }
            
            // Create the EPUB
            if ($epub->error) 
                dd($epub->error);

            $epub->CreateEPUB();
            $file = public_path($epub->epub_file);

            if ($epub->error)
                dd($epub->error);

            // recursive to double check if file is realy generated
            return self::generateEPub($id, $book);
        } catch (\Exception $e) {
            dd('Whoops: '. $e->getMessage());
        }
    }

    public static function generateCoverPDF($book, $name = 'front', $exact_path = '')
    {
        $url = env('APP_URL');

        $cover = '';
        $hasCover = false;
        self::getCover($book['user_id'], $book['id'], $name, $hasCover, $cover);

        if(!$hasCover)
            return null;

        // Generate cover PDF
        $viewTemplate = 'pdf.cover';
        if($name == 'copy-spine')
            $viewTemplate = 'pdf.spine-cover';

        $cover = PDF::loadView($viewTemplate, [
            'title' => $book['title'],
            'author' => $book['author'],
            'cover' => $url.$cover
        ])
        ->save($exact_path.'cover_'.$name.'.pdf');

        return public_path($exact_path.'cover_'.$name.'.pdf');
    }

    public static function generatePDF($id, $book)
    {
        $chapters = [];
        $title = $book['title'];
        $author = $book['author'];
        $tableOfContentData = [];


        $subfolder_path = 'pdf';
        $exact_path = 'storage/'.$subfolder_path.'/'.$id;
        $header = '';

        if(Storage::has('public/pdf/'.$id.'/'.$title.'.pdf')){
            return public_path($exact_path.'/'.$title.'.pdf');
        }

        if(!file_exists($exact_path.'/tmp')){
            \File::makeDirectory($exact_path.'/tmp', 0777, true);
        }

        $frontCoverUrl = self::generateCoverPDF($book, 'front', $exact_path.'/tmp/');
        $spineCoverUrl = self::generateCoverPDF($book, 'copy-spine', $exact_path.'/tmp/');
        $backCoverUrl = self::generateCoverPDF($book, 'back', $exact_path.'/tmp/');

        // Merge PDF
        $newpdf = new \PDFMerger();

        $page_number = 1;
        $pdf_paths = [];
        if($book['chapters']){
            foreach ($book['chapters'] as $key => $chapter) {
                $pageId = 'ch'.$key;
                $pageTitle = $chapter['title'];
                $tableOfContentData[] = [
                    'id' => $pageId,
                    'title' => $pageTitle,
                    'page_number' => $page_number.' - '.(intval($page_number) + (!$chapter['is_file_type']?count($chapter['pages']):$chapter['total_page']) - 1),
                ];
                $page_number = intval($page_number) + (!$chapter['is_file_type']?count($chapter['pages']):$chapter['total_page']);

                $pdf = PDF::loadView('pdf.page-title', [
                    'title' => $title,
                    'author' => $author,
                    'page_title' => $pageTitle,
                ])->save($exact_path.'/tmp/pdf-'.$key.'-body_content.pdf');
                $pdf_paths[] = public_path($exact_path.'/tmp/pdf-'.$key.'-body_content.pdf');

                // merge story with exists pdf file
                // create pdf per chapter and mergis_file_typee it
                if($chapter['is_file_type']){

                    $pdf_paths[] = public_path($chapter['file_url']);
                    continue;
                }
                
                if(!$chapter['pages'])
                    continue;

                $new_chapter = [];
                foreach ($chapter['pages'] as $page) {

                    $content = $page['content'];
                    $found = preg_match_all('/(src)=("[^"]*")/i',$content, $results);

                    if($found){
                        if(!empty($results[2]))
                            foreach ($results[2] as $key => $result) {
                                $oldSrc = str_replace('"', '', $result);
                                $content = str_replace($oldSrc,public_path().$oldSrc,$content);
                            }
                    }

                    $content = preg_replace('/figure/i', "div", $content);
                    $new_chapter[$pageTitle][] = $content;
                }


                // Generate Draft Care Plan PDF
                $pdf = PDF::loadView('pdf.chapter', [
                    'title' => $title,
                    'author' => $author,
                    'chapter' => $new_chapter,
                ])->save($exact_path.'/tmp/'.$key.'-body_content.pdf');
                $contentUrl = public_path($exact_path.'/tmp/'.$key.'-body_content.pdf');

                $pdf_paths[] = $contentUrl;
                // $newpdf->addPDF($contentUrl, 'all');
            }
        }

        $pdfContentData = [
            'title' => $title,
            'author' => $author,
            'tableOfContent' => $tableOfContentData,
        ];

        // Generate Draft Care Plan PDF
        $pdf = PDF::loadView('pdf.index', $pdfContentData)
            ->save($exact_path.'/tmp/header.pdf');
        // $contentUrl = public_path($exact_path.'/tmp/header.pdf');
        array_splice($pdf_paths, 0, 0, public_path($exact_path.'/tmp/header.pdf'));

        if($frontCoverUrl)
            $newpdf->addPDF($frontCoverUrl, '1');

        foreach ($pdf_paths as $key => $path) {
            $newpdf->addPDF($path, 'all');
        }

        if($spineCoverUrl)
            $newpdf->addPDF($spineCoverUrl, '1');

        if($backCoverUrl)
            $newpdf->addPDF($backCoverUrl, '1');
        
        $newpdf->merge('file', $exact_path.'/'.$title.'.pdf');

        return public_path($exact_path.'/'.$title.'.pdf');
    }

    public static function mapData($arg, $ids, $key){
        if($arg['id'] == $key)
            return true;

        return $arg ? self::mapData(next(array_values($ids)), $ids, $key) : false;
    }
}