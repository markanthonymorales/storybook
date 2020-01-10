<?php
namespace App\Helper;

use Zipper;
use Storage;
use File;

/**
 * TPEpubCreator - PHP EPUB Creator
 *
 * This PHP class creates e-books using the EPUB standard format. An example can
 * be found at ../index.php.
 *
 * @package  TPEpubCreator
 * @author   Luiz Otávio Miranda <contato@tutsup.com>
 * @version  $Revision: 1.0.0 $
 * @access   public
 * @see      http://www.tutsup.com/ 
 */
class TPEpubCreator
{
    /**
     * This is the abspath for this file
     *
     * @access private
     * @var string
     * @since 1.0.0
     */
    private $abspath;
    
    /**
     * This is the cover img path
     *
     * @access private
     * @var string
     * @since 1.0.0
     */
    private $cover_img;
    
    /**
     * This is the content.opf file
     *
     * @access private
     * @var array
     * @since 1.0.0
     */
    private $opf = array();
    
    /**
     * This is the toc.ncx file
     *
     * @access private
     * @var array
     * @since 1.0.0
     */
    private $ncx = array();
    /**
     * This is the pages array
     *
     * @access private
     * @var array
     * @since 1.0.0
     */
    private $pages = array();
    
    /**
     * This is the images array
     *
     * @access private
     * @var array
     * @since 1.0.0
     */
    private $images = array();

    /**
     * This is the images array
     *
     * @access private
     * @var array
     * @since 1.0.0
     */
    public $content_images = array();
    
    /**
     * This is the new images array
     *
     * @access private
     * @var array
     * @since 1.0.0
     */
    private $new_images = array();
    
    /**
     * This is to check if a cover has been added
     *
     * @access private
     * @var bool
     * @since 1.0.0
     */
    private $cover;
    
    /**
     * This is our errors output
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $error;
    
    /**
     * This is the book's uuid
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $uuid;
    
    /**
     * This is the book's title
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $title = 'Untitled';
    
    /**
     * This is the book's creator
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $creator = 'Storybook.com';
    
    /**
     * This is the book's language
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $language = 'en';
    
    /**
     * This is the book's rights
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $rights = 'Public Domain';
    
    /**
     * This is the book's publisher
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $publisher = 'http://laravel.local/';
    
    /**
     * This is the book's css
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $css;
    
    /**
     * This is the temp folder used to store the book's files before zip it
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $temp_folder;
    
    /**
     * This is the path to output the epub file
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $epub_file;
    
    /**
     * This is the container.xml file
     *
     * @access public
     * @var string
     * @since 1.0.0
     */
    public $container;
    
    /**
     * This is the key for the pages array
     *
     * @access public
     * @var int
     * @since 1.0.0
     */
    public $key = 0;
    
    /**
     * This is the key for the images array
     *
     * @access public
     * @var int
     * @since 1.0.0
     */
    public $image_key = 0;
    
    /**
     * Constructor.
     *
     * This only sets the abspath and uuid
     *
     * @since 1.0.0
     * @access public
     *
     */   
    public function __construct () {
        $this->abspath = dirname( __FILE__ );
        $this->uuid = md5( microtime() );
    }
    
    /**
     * Add Image
     *
     * This stores the images in the images array and set the cover.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $path Image's Path
     * @param string $type Image's Mime-type
     * @param bool $cover Whether it will or will not be a cover
     *
     * @return bool false if the image does not exists
     */    
    public function AddImage( $path = false, $type = false, $cover = 0 ) {
        $this->image_key++;
        
        // Checks if the image exists first
        if ( ! Storage::has($path) ) {
            $this->error = 'Cannot find image ' . $path . '.';
            return;
        }
        
        $this->images[$this->image_key]['path'] = $path;
        $this->images[$this->image_key]['type'] = $type;
        $this->images[$this->image_key]['cover'] = $cover;
    }
    
    /**
     * Add Page
     *
     * This stores the pages in the pages array. It'll store the XHTML content
     * for the page, but won't check or parse it.
     *
     * @since 1.0.0
     * @access public
     *
     * @param string $content Page content (XHTML)
     * @param string $file A file that has the page content (XHTML)
     * @param string $title Page's title
     * @param bool $download_images Whether to download images from the HTML or not
     *
     * @return bool false if the image does not exists
     */    
    public function AddPage( 
        $content = null, 
        $file = null, 
        $title = 'Untitled',
        $download_images = false
    ) {
        // Set the key for the page
        $this->key++;

        // If nothing to add, nothing to do
        if ( ! $content && ! $file ) {
            $this->error = 'No content or file added.';
            return;
        }
        
        // If it's XHTML
        if ( $content ) {
            $this->pages[$this->key]['content'] = $content;
        }
        
        // If it's a file
        if ( $file ) {
            // If the file does not exists, won't do anything
            if (!Storage::has($file) ) {
                Storage::put($file, '');
            }

            $file = Storage::get($file);
            $this->pages[$this->key]['content'] = $file;
        }
        
        // If the $download_images param is set to true, we'll try to download
        // images found and add it to you e-book
        if ( $download_images ) {
            $found_images = preg_match_all(
                '/(\<img.*?src=[\'|"])(.*?)([\'|"].*?\>)/mis', 
                $this->pages[$this->key]['content'], 
                $image_matches
            );
            
            // Just need the URLs
            if ( $found_images ) {
                if ( ! empty( $image_matches[2] ) ) {
                    foreach ( $image_matches[2] as $img ) {
                        $this->AddImage( $img );
                    }
                }
            }
        }

        // Set the page title
        $this->pages[$this->key]['title'] = $title;        
    }
    
    /**
     * Create EPUB
     *
     * This creates the epub file. It uses lots of other methods to accomplish
     * its task.
     *
     * @since 1.0.0
     * @access public
     */        
    public function CreateEPUB() {
        // Creates all the folders needed
        $this->CreateFolders();

        $this->CopyContentImages();
        // If there's no error we're good to go
        if ( $this->error ) {
            return;
        }
        
        // Open the content.opf file
        $this->OpenOPF();

        // Open the toc.ncx file
        $this->OpenNCX();
        
        // Open the css.css file
        $this->OpenCSS();
        
        // Variables needed to put everything in the right place
        $ncx = null;
        $opf = null;
        $fill_opf_spine = null;
        
        // Loop the pages array and fill the content.opf and toc.ncx content
        foreach( $this->pages as $key => $value ) {
            // The page
            $page = 'page' . $key;
            
            // OPF
            $opf .= '<item id="' . $page . '" href="' . $page . '.xhtml" media-type="application/xhtml+xml" />' . "\r\n";
            
            // NCX
            $ncx  .= '<navPoint id="' . $page . '" playOrder="' . $key . '">' . "\r\n";
            $ncx .= '<navLabel>' . "\r\n";
            $ncx .= '<text>' . $value['title'] . '</text>' . "\r\n";
            $ncx .= '</navLabel>' . "\r\n";
            $ncx .= '<content src="' . $page . '.xhtml"/>' . "\r\n";
            $ncx .= '</navPoint>' . "\r\n";
            
            // Fill the spine
            $fill_opf_spine .= '<itemref idref="' . $page . '"  linear="yes"/>' . "\r\n";
        }
        
        // If there are images, loop the values
        if ( ! empty( $this->images ) ) {
            foreach( $this->images as $image_key => $image_value ) {
                
                // New image have the same name as the old one
                $new_image  = $this->temp_folder . '/OEBPS/images/';
                // $new_image .= mt_rand(0,9999) . '_';
                $new_image .= basename( $image_value['path'] );
                
                // Mime-type
                $image_type = $image_value['type'];
                
                // If we don't have a mimetype for the image
                // We'll try to get it
                if ( ! $image_type ) {
                    $image_type = getimagesize( $image_value['path'] );
                    $image_type = $image_type['mime'];
                }
                
                // Try to copy the image
                if(!Storage::copy($image_value['path'], $new_image)){
                // if ( ! @copy( $image_value['path'], $new_image ) ) {
                    $this->error = 'Cannot copy ' . $image_value . '.';
                    return;
                }
                
                // Set the new images name
                $this->new_images[$image_key]['path'] = $new_image;
                
                // If there is a cover, create another ID and XHTML page later
                if ( ! empty( $image_value['cover'] ) ) {
                    $opf .= '<item id="cover" href="cover.xhtml" media-type="application/xhtml+xml" />' . "\r\n";
                    $opf .= '<item id="cover-image';
                    $this->cover_img = basename( $new_image );
                } else {
                    $opf .= '<item id="img' . $image_key;
                }
                
                // End the image <item> tag
                $opf .= '" href="images/' . basename( $new_image );
                $opf .= '" media-type="' . $image_type . '" />' . "\r\n";
            }
        }
        
        // Fill the NCX and OPF
        $this->ncx[] = $ncx;
        
        $this->opf[] = $opf;
        $this->opf[] = '</manifest><spine toc="ncx">' . "\r\n";
        
        // If there's a cover, we'll need an <itemref idref="cover" />
        if ( $this->cover_img ) {
            $this->opf[] = "<itemref idref=\"cover\" linear=\"no\"/>\r\n";
        }
        
        // Fill the spine
        $this->opf[] = $fill_opf_spine;
        
        // Closes the OPF and NCX
        $this->CloseOPF();
        $this->CloseNCX();
        
        // Create the OPF and NCX files
        $this->CreateOPF();
        $this->CreateNCX();
        
        // XHTML default page header
        $page_content  = "<?xml version='1.0' encoding='utf-8'?>" . "\r\n";
        $page_content .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\r\n";
        $page_content .= '<html xmlns="http://www.w3.org/1999/xhtml">' . "\r\n";
        $page_content .= '<head>' . "\r\n";
        $page_content .= '<meta content="application/xhtml+xml; charset=utf-8" http-equiv="Content-Type"/>' . "\r\n";
        $page_content .= '<link href="css.css" type="text/css" rel="stylesheet"/>' . "\r\n";
        $page_content .= '</head>' . "\r\n";
        $page_content .= '<body>' . "\r\n";
        
        // Loop the pages
        foreach( $this->pages as $key => $value ) {
            
            // Page file
            $page = 'page' . $key . '.xhtml';
            
            // Replace unwanted tags (for now scripts and iframes)
            $value['content'] = preg_replace(
                '/\<(script|iframe)[^>]*\>.*?\<\/(script|iframe)\>/mis', 
                '', 
                $value['content']
            );

            // Fill the page content and ends the XHTML
            $value['content']  = $page_content . $value['content'];
            $value['content'] .= '</body></html>';
            
            // Replace the HTML images to the new images
            foreach( $this->images as $check_image_key => $check_images ) {
                $value['content'] = str_replace ( 
                    $check_images['path'],
                    'images/' . basename( $this->new_images[$check_image_key]['path'] ),
                    $value['content']
                );
            }
            
            // Create the file
            $this->CreateFile( $this->temp_folder . '/OEBPS/' . $page, $value['content'] );
        }
        
        // If there's a cover, create its page
        if ( ! empty( $this->cover_img )  ) {
            $cover_page  = $page_content;
            $cover_page .= '<img class="cover-image" width="600" height="800" src="images/' . $this->cover_img . '" />' . "\r\n";
            $cover_page .= '</body></html>';
            $this->CreateFile( $this->temp_folder . '/OEBPS/cover.xhtml', $cover_page );
        }

        // Create the zip file
        $this->CreateZip();
    }

    private function CopyContentImages(){
        if(!empty($this->content_images)){
            foreach ($this->content_images as $key => $image) {
                try {
                    if(!File::copy($image['oldSrc'], $image['newSrc'])){
                        $this->error = 'Cannot copy ' . $image['oldSrc'] . '.';
                        return;
                    } 
                } catch (\Exception $e) {
                    dd('Whoops: '.$e->getMessage());
                }
            }
        }
    }
    
    /**
     * Open CSS
     *
     * It will simply fill the $css property
     */    
    public function OpenCSS() {
        if ( ! $this->css ) {
            $this->css  = 'body {';
            $this->css .= 'margin-left: .5em;';
            $this->css .= 'margin-right: .5em;';
            $this->css .= 'text-align: left;';
            $this->css .= 'direction: ltr;';
            $this->css .= 'font-family: "Nunito", sans-serif;';
            $this->css .= 'direction: ltr;';
            $this->css .= 'font-size: 0.9rem;';
            $this->css .= 'font-weight:400;';
            $this->css .= 'line-height: 1.6;';
            $this->css .= 'color: #212529;';
            $this->css .= '} ';

            $this->css .= '*, *::before, *::after {';
            $this->css .= 'box-sizing: border-box;';
            $this->css .= '} ';

            $this->css .= '.cover-image {';
            $this->css .= 'width: auto !important;';
            $this->css .= 'height: 100% !important;';
            $this->css .= '} ';

            $this->css .= 'div.table-of-content {';
            $this->css .= 'margin: auto;';
            $this->css .= 'width: 100%;';
            $this->css .= 'padding: 10px;';
            $this->css .= 'height: 100%;';
            $this->css .= 'padding-top: 100px;';
            $this->css .= '} ';

            $this->css .= 'div.table-of-content h1 {';
            $this->css .= 'text-align: center;';
            $this->css .= 'margin-bottom: 35px;';
            $this->css .= 'font-weight: 700;';
            $this->css .= '} ';

            $this->css .= 'div.table-of-content table {';
            $this->css .= 'width: 100%;';
            $this->css .= '} ';

            $this->css .= 'div.table-of-content table td h5 {';
            $this->css .= 'display: inline-block;';
            $this->css .= 'font-size: 1em;';
            $this->css .= 'margin-top: 20px;';
            $this->css .= '} ';

            $this->css .= 'div.table-of-content table td i {';
            $this->css .= 'display: inline-block;';
            $this->css .= 'border-bottom: 1px dotted #000;';
            $this->css .= 'height: 17px;';
            $this->css .= '-webkit-flex: 1; /* Safari 6.1+ */';
            $this->css .= '-ms-flex: 1; /* IE 10 */';
            $this->css .= 'flex: 1;';
            $this->css .= '-webkit-margin-before: 1.3em;';
            $this->css .= '} ';

            $this->css .= 'div.table-of-content table td font {';
            $this->css .= 'display: inline-block;';
            $this->css .= '-webkit-margin-before: 1.2em;';
            $this->css .= '} ';

            $this->css .= 'div.table-of-content table td {';
            $this->css .= 'padding-bottom: 15px;';
            $this->css .= 'width: 100%;';
            $this->css .= 'display: flex;';
            $this->css .= 'display: -webkit-flex;';
            $this->css .= '} ';

            $this->css .= 'p {';
            $this->css .= 'word-break: break-word;';
            $this->css .= 'line-height: 1.6 !important;';
            $this->css .= 'display: block;';
            $this->css .= 'margin: 0;';
            $this->css .= '} '; 
            

            $this->css .= 'article, aside, figcaption, figure, footer, header, hgroup, main, nav, section {';
            $this->css .= 'display: block;';
            $this->css .= '} ';

            $this->css .= 'figure {';
            $this->css .= 'margin: 0 0 1rem;';
            $this->css .= '} ';

            $this->css .= 'figure.image {';
            $this->css .= 'width: 100%;';
            $this->css .= 'height: auto;';
            $this->css .= 'margin-bottom: 5px;';
            $this->css .= '} ';

            $this->css .= 'img {';
            $this->css .= 'vertical-align: middle;';
            $this->css .= 'border-style: none;';
            $this->css .= '} ';

            $this->css .= 'figure.image img {';
            $this->css .= 'width: 100%;';
            $this->css .= 'height: auto;';
            $this->css .= '} ';

            $this->css .= '.ck-content .image>img {';
            $this->css .= 'display: block;';
            $this->css .= 'margin: 0 auto;';
            $this->css .= 'max-width: 100%;';
            $this->css .= '} ';

            $this->css .= '.ck .ck-widget {';
            $this->css .= 'outline-width: 3px;';
            $this->css .= 'outline-style: solid;';
            $this->css .= 'outline-color: transparent;';
            $this->css .= 'transition: outline-color 200ms ease;';
            $this->css .= '} ';

            $this->css .= '.ck-content .image {';
            $this->css .= 'clear: both;';
            $this->css .= 'text-align: center;';
            $this->css .= 'margin: 1em 0;';
            $this->css .= '} ';

            $this->css .= '.ck-content .image {';
            $this->css .= 'position: relative;';
            $this->css .= 'overflow: hidden;';
            $this->css .= '} ';

            $this->css .= '.ck-content .image-style-align-left {';
            $this->css .= 'float: left;';
            $this->css .= 'margin-right: 1.5em;';
            $this->css .= '} ';

            $this->css .= '.ck-content .image-style-align-center, .ck-content ';
            $this->css .= '.image-style-align-left, .ck-content .image-style-align-right, ';
            $this->css .= '.ck-content .image-style-side {';
            $this->css .= 'max-width: 50%;';
            $this->css .= '} ';

            $this->css .= '.ck-content .image-style-align-right { ';
            $this->css .= 'float: right;';
            $this->css .= 'margin-left: 1.5em;';
            $this->css .= '} ';

            $this->css .= '.ck.ck-editor__editable_inline>:first-child {';
            $this->css .= 'margin-top: calc(0.6em*1.5);';
            $this->css .= '} ';

            $this->css .= '.pen-green, .pen-red {';
            $this->css .= 'background-color: transparent;';
            $this->css .= '} ';

            $this->css .= '.pen-red {';
            $this->css .= 'color: #e91313;';
            $this->css .= '} ';

            $this->css .= '.pen-green {';
            $this->css .= 'color: #180;';
            $this->css .= '} ';

            $this->css .= '.marker-blue {';
            $this->css .= 'background-color: #72cdfd;';
            $this->css .= '} ';

            $this->css .= '.marker-pink {';
            $this->css .= 'background-color: #fc7999;';
            $this->css .= '} ';

            $this->css .= '.marker-green {';
            $this->css .= 'background-color: #63f963;';
            $this->css .= '} ';
            $this->css .= '.marker-yellow {';
            $this->css .= 'background-color: #fdfd77;';
            $this->css .= '} ';

            $this->css .= 'mark, .mark {';
            $this->css .= 'padding: 0.2em;';
            $this->css .= 'background-color: #fcf8e3;';
            $this->css .= '} ';

            $this->css .= '.text-tiny {';
            $this->css .= 'font-size: .7em;';
            $this->css .= '} ';

            $this->css .= '.text-small {';
            $this->css .= 'font-size: .85em;';
            $this->css .= '} ';

            $this->css .= '.text-big {';
            $this->css .= 'font-size: 1.4em;';
            $this->css .= '} ';

            $this->css .= '.text-huge {';
            $this->css .= 'font-size: 1.8em;';
            $this->css .= '}';
        }
    }
    
    /**
     * Open OPF
     *
     * Fill the content.opf file ($opf property)
     */    
    private function OpenOPF() {
        $this->opf[] = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
        $this->opf[] = '<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="BookID" version="2.0" >' . "\r\n";
        $this->opf[] = '<metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">' . "\r\n";
        $this->opf[] = '<dc:title>' . $this->title . '</dc:title>' . "\r\n";
        $this->opf[] = '<dc:creator opf:file-as="' . $this->creator . '" opf:role="aut">' . $this->creator . '</dc:creator>' . "\r\n";
        $this->opf[] = '<dc:language>' . $this->language . '</dc:language>' . "\r\n";
        $this->opf[] = '<dc:rights>' . $this->rights . '</dc:rights>' . "\r\n";
        $this->opf[] = '<dc:publisher>' . $this->publisher . '</dc:publisher>';
        $this->opf[] = '<dc:identifier id="BookID" opf:scheme="UUID">' . $this->uuid . '</dc:identifier>' . "\r\n";
        $this->opf[] = '<meta name="cover" content="cover" />' . "\r\n";
        $this->opf[] = '</metadata><manifest>' . "\r\n";
        $this->opf[] = '<item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml" />' . "\r\n";
        $this->opf[] = '<item id="style" href="css.css" media-type="text/css" />' . "\r\n";
    }
    
    /**
     * Close OPF
     *
     * End of the content.opf file
     */    
    private function CloseOPF() {
        $this->opf[] = '</spine></package>' . "\r\n";
    }
    
    /**
     * Create OPF
     *
     * Creates the content.opf file
     */    
    private function CreateOPF() {
        $opf = null;
        
        foreach( $this->opf as $lines ) { 
            $opf .= "$lines\r\n";
        }
        
        $this->CreateFile( $this->temp_folder . '/OEBPS/content.opf', $opf );
    }
    
    /**
     * Open NCX
     *
     * Fill the toc.ncx content ($ncx property)
     */    
    private function OpenNCX() {
        $this->ncx[] = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
        $this->ncx[] = '<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">' . "\r\n";
        $this->ncx[] = '<meta name="dtb:uid" content="' . $this->uuid . '"/>' . "\r\n";
        $this->ncx[] = '<head>' . "\r\n";
        $this->ncx[] = '<meta name="dtb:depth" content="1"/>' . "\r\n";
        $this->ncx[] = '<meta name="dtb:totalPageCount" content="0"/>' . "\r\n";
        $this->ncx[] = '<meta name="dtb:maxPageNumber" content="0"/>' . "\r\n";
        $this->ncx[] = '</head>' . "\r\n";
        $this->ncx[] = '<docTitle><text>' . $this->title . '</text></docTitle>' . "\r\n";
        $this->ncx[] = '<navMap>' . "\r\n";
    }
    
    /**
     * Close NCX
     *
     * Closes the toc.ncx file content
     */    
    private function CloseNCX() {
        $this->ncx[] = '</navMap>' . "\r\n";
        $this->ncx[] = '</ncx>' . "\r\n";
    }
    
    /**
     * Create NCX
     *
     * Creates toc.ncx file
     */    
    private function CreateNCX() {
        $ncx = null;
        
        foreach( $this->ncx as $lines ) { 
            $ncx .= "$lines\r\n";
        }
        
        $this->CreateFile( $this->temp_folder . '/OEBPS/toc.ncx', $ncx );
    }
    
    /**
     * Create folders
     *
     * Create all the temp folders needed
     */    
    private function CreateFolders() {
        
        // If the user do not specify a temp folder, we'll assume it.
        if ( ! $this->temp_folder ) {
            $this->temp_folder = preg_replace( '/[^A-Za-z0-9]/is', '', $this->title );
            $this->temp_folder = strtolower( $this->temp_folder );
        }
        
        // Temp folder is the book's uuid
        $this->temp_folder .= $this->uuid . '/';

        // Check to see if there's no folder with the same name
        if(!Storage::has($this->temp_folder) ) {
            // Creates the main temp folder
            Storage::makeDirectory($this->temp_folder, 0777);

            // Check the folder
            if (!Storage::has($this->temp_folder)) {
                $this->error = "Cannot create EPUB folder \"{$this->temp_folder}\".";
                return;
            }
        }
        
        // Creates the other needed folders
        Storage::makeDirectory($this->temp_folder.'/META-INF', 0777);
        Storage::makeDirectory($this->temp_folder.'/OEBPS', 0777);
        Storage::makeDirectory($this->temp_folder.'/OEBPS/images', 0777);
            
        // Open the CSS
        $this->OpenCSS();
        
        // Creates the container.xml
        $this->CreateContainer();
        
        // Creates the needed epub files
        $this->CreateFile( $this->temp_folder . '/mimetype', 'application/epub+zip');
        $this->CreateFile( $this->temp_folder . '/OEBPS/css.css', $this->css);
        $this->CreateFile( $this->temp_folder . '/META-INF/container.xml', $this->container);
    }
    
    /**
     * Create container
     *
     * Creates the container.xml file
     */    
    private function CreateContainer() {
        $this->container  = '<?xml version="1.0" encoding="UTF-8" ?>';
        $this->container .= '<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">';
        $this->container .= '<rootfiles>';
        $this->container .= '<rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>';
        $this->container .= '</rootfiles>';
        $this->container .= '</container>';
    }
    
    /**
     * Create Files
     */    
    private function CreateFile( $file, $content = null ) {
        // $handle = fopen( $file, 'w+' );
        // $ler = fwrite( $handle, $content );
        // fclose($handle);
        Storage::put($file, $content);
    }
    
    /**
     * Create Zip
     *
     * This creates the zip file as epub.
     *
     * @since 1.0.0
     * @access private
     */        
    private function CreateZip () {
        // Checks the zip extension
        if (!extension_loaded('zip')) {
            $this->error = 'zip extension is not loaded';
            return false;
        }
        
        // If the user do not specify the epub file, we'll assume it.
        if (!$this->epub_file) {
            $this->epub_file  = preg_replace( '/[^A-Za-z0-9]/is', '', $this->title );
            $this->epub_file .= '.epub';
        }

        $files = glob(public_path('storage/temp_folder/'.$this->uuid.'/'));
            
        Zipper::make($this->epub_file)->add($files)->getStatus();
    }
}