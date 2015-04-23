<?php

    /**
     *  Article plugin
     *
     *  @package Promo
     *  @subpackage Plugins
     *  @author Yudin Evgeniy / JINN
     *  @copyright 2012-2015 Yudin Evgeniy / JINN
     *  @version 1.0.0
     *
     */


    // Register plugin
    Plugin::register( __FILE__,                    
                    __('Article', 'article'),
                    __('Article plugin for Promo CMS', 'article'),  
                    '1.0.0',
                    'JINN',
                    'http://cms.promo360.ru/',
                    'article');

    // Load Article Admin for Editor and Admin
    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {
        Plugin::admin('article');
    }
    
    Stylesheet::add('plugins/article/lib/style.css', 'frontend', 11);
    Stylesheet::add('plugins/article/lib/admin.css', 'backend', 11);
    
    Shortcode::add('article', 'Article::_shortcode');
    
    class Article extends Frontend {
        
        public static $article = null; // article table @object
        public static $meta = array(); // meta tags article @array
        public static $template = ''; // article template content @string
        
        public static function main(){
                
            Article::$article = new Table('article');
             
            Article::$meta['title'] = __('Article', 'article');
            Article::$meta['keywords'] = '';
            Article::$meta['description'] = '';
            
            Breadcrumbs::add(Site::url().'/article', __('Article', 'article'));
                
            $uri = Uri::segments();
            
            if(empty($uri[1]) or ($uri[1] == 'page')) {
                Article::getArticle($uri);
            } elseif (intval($uri[1]) > 0 and isset($uri[2])) {
                Article::getArticleCurrent($uri);
            }
        }
        
        /** 
         * Last article
         * <ul><?php echo Article::last(3);?></ul>
         */
        public static function last($count=3){
            $site_url = Option::get('siteurl');
            Article::$article = new Table('article');
            
            $records_all = Article::$article->select('[status="published"]', 'all', null, array('id','slug','name', 'date'));
            $records_sort = Arr::subvalSort($records_all, 'date', 'DESC');
            
            if(count($records_sort)>0) {
                $records = array_slice($records_sort, 0, $count); 
                
                $view = View::factory('article/views/frontend/last');
                $view->assign('records', $records)->assign('site_url', $site_url);
                $output = $view->render();
                
                return $output;
            }
        }
        
        /**
         * Shortcode article
         * <ul>{article count=3}</ul>
         */
        public static function _shortcode($attributes) {
            extract($attributes);
        
            $count = (isset($count)) ? (int)$count : 3;
        
            return Article::last($count);
        }
        
        /**
         * get Article List
         */
        public static function getArticle($uri){
        
            $site_url = Option::get('siteurl');
            $limit    = Option::get('article_limit');
            
            $records_all = Article::$article->select('[status="published"]', 'all', null, array('id','slug','name', 'date', 'img'));
            
            $count_article = count($records_all);
            
            $pages = ceil($count_article/$limit);
            
            $page = (isset($uri[1]) and isset($uri[2]) and $uri[1] == 'page') ? (int)$uri[2] : 1;
            
            if($page < 1 or $page > $pages) {
                Article::error404();
            } else {
            
                $start = ($page-1)*$limit;

                $records_sort = Arr::subvalSort($records_all, 'date', 'DESC');
                
                if($count_article > 0) $records = array_slice($records_sort, $start, $limit);  
                else $records = array();
                
                $img_w = Option::get('article_w');
                
                if (file_exists(ROOT . DS . 'public' . DS . 'article' . DS . 'default.jpg')) {
                    $img_default = '<img class="article-avatar" src="'.$site_url.'/public/article/default.jpg" width="'.$img_w.'" alt=""/>';
                } else {
                    $img_default = '';
                }
                
                Article::$template = View::factory('article/views/frontend/index')
                    ->assign('records', $records)
                    ->assign('site_url', $site_url)
                    ->assign('current_page', $page)
                    ->assign('pages_count', $pages)
                    ->assign('img_w', $img_w)
                    ->assign('img_def', $img_default)
                    ->render();
            }
        }
        
        /**
         * get Current article
         */
        public static function getArticleCurrent($uri){
            $site_url = Option::get('siteurl');
            
            $id = intval($uri[1]);
            $slug = $uri[2];
                    
            $records = Article::$article->select('[id='.$id.']', null);
                
            if($records) {
                if($records['slug'] == $slug) {
                
                    if(empty($records['title'])) $records['title'] = $records['name'];
                    if(empty($records['h1']))    $records['h1']    = $records['name'];
                
                    Article::$meta['title'] = $records['title'];
                    Article::$meta['keywords'] = $records['keywords'];
                    Article::$meta['description'] = $records['description'];
                    
                    $img_w = Option::get('article_w');
                    $img = '';
                    
                    if(!empty($records['img'])) {
                        if(file_exists(ROOT . DS . 'public' . DS . 'article' . DS . 'images' . DS . $records['img'])) {
                            $img = '<img class="article-avatar" src="'.$site_url.'/public/article/images/'.$records['img'].'" width="'.$img_w.'" alt=""/>';
                        }
                    }
                    
                    Breadcrumbs::add(Site::url()."/article/{$id}/{$slug}", $records['name']);
                        
                    Article::$template = View::factory('article/views/frontend/current')
                        ->assign('row', $records)
                        ->assign('site_url', $site_url)
                        ->assign('img', $img)
                        ->render();
                } else {
                    Article::error404();
                }
            } else {
                Article::error404();
            }
        }
        
        public static function title(){
            return Article::$meta['title'];
        }

        public static function keywords(){
            return Article::$meta['keywords'];
        }

        public static function description(){
            return Article::$meta['description'];
        }

        public static function content(){
            return Article::$template;
        }

        public static function template() {
            return Option::get('article_template');
        }
        
        public static function error404() {
            if (BACKEND == false) {
                Article::$template = Text::toHtml(File::getContent(STORAGE . DS . 'pages' . DS . '1.page.txt'));
                Article::$meta['title'] = 'error404';
                Response::status(404);
            }
        }
        
        public static function getContentShort($id, $short=true, $full_article='') {
            $text = Text::toHtml(File::getContent(STORAGE . DS . 'article' . DS . $id . '.article.txt'));
            
            if($short) {
                $content_array = explode("{cut}", $text);
                $content = $content_array[0];
                if(count($content_array)>1) $content.= '<a href="'.$full_article.'" class="article-more">'.__('Read more', 'article').'</a>';
            } else {
                $content = strtr($text, array('{cut}' => ''));
            }
            
            return Filter::apply('content', $content);
        }
        
        /**
         * current page
         * pages all
         * site_url
         * limit pages
         */
        public static function paginator($current, $pages, $site_url, $limit_pages=10) {
            
            if ($pages > 1) {
            
                // pages count > limit pages
                if ($pages > $limit_pages) {
                    $start = ($current <= 6) ? 1 : $current-3;
                    $finish = (($pages-$limit_pages) > $current) ? ($start + $limit_pages - 1) : $pages;
                } else {
                    $start = 1;
                    $finish = $pages;
                }

                // pages title
                echo '<strong>'.__('Pages:', 'article').'</strong> &nbsp; < ';
                
                // prev
                if($current==1){ echo __('Prev', 'article');} 
                else { echo '<a href="'.$site_url.($current-1).'">'.__('Prev', 'article').'</a> '; } echo '&nbsp; ';
                
                // next
                if($current==$pages){ echo __('Next', 'article'); }
                else { echo '<a href="'.$site_url.($current+1).'">'.__('Next', 'article').'</a> '; } echo ' > ';
    
                // pages list
                echo '<div id="article-page">';
                
                    if (($pages > $limit_pages) and ($current > 6)) {
                        echo '<a href="'.$site_url.'1">1</a>';
                        echo '<span>...</span>'; 
                    }
                
                    for ($i = $start; $i <= $finish; $i++) {
                        $class = ($i == $current) ? ' class="current"' : '';
                        echo '<a href="'.$site_url.$i.'"'.$class.'>'.$i.'</a>'; 
                    }
                
                    if (($pages > $limit_pages) and ($current < ($pages - $limit_pages))) {
                        echo '<span>...</span>'; 
                        echo '<a href="'.$site_url.$pages.'">'.$pages.'</a>';
                    }
                echo '</div>';
            }
        }
    }